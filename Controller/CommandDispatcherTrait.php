<?php

namespace Mechanic\CqrsKit\Controller;

use Mechanic\CqrsKit\Messenger\CommandDispatcher;
use Mechanic\CqrsKit\Messenger\Exception\DomainException;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormError;
use Symfony\Contracts\Service\Attribute\Required;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Throwable;

trait CommandDispatcherTrait
{
    private CommandDispatcher $commandDispatcher;

    #[Required]
    public function setCommandDispatcher(CommandDispatcher $commandDispatcher): void
    {
        $this->commandDispatcher = $commandDispatcher;
    }

    protected function execute($command): mixed
    {
        return $this->commandDispatcher->execute($command);
    }

    /**
     * @param FormInterface $form
     * @param callable      $commandFactory Может принимать единственный аргумент - результат, возвращаемый формой
     * @param bool          $catchDomainException Ловить доменные исключения и выводить их в виде ошибки формы
     *
     * @return bool|mixed|null
     * @throws Throwable Если в стэке исключений есть \DomainException, оно выводится как ошибка формы
     */
    protected function handleForm(FormInterface $form, callable $commandFactory, bool $catchDomainException = true): mixed
    {
        if ($form->isSubmitted() && $form->isValid()) {
            $formDto = $form->getData();
            $command = $commandFactory($formDto);

            try {
                return $this->execute($command) ?? true;
            } catch (Throwable $ex) {
                if (true !== $catchDomainException) {
                    throw $ex;
                }

                $rootCause = $this->extractRootCauseException($ex);

                if ($rootCause instanceof DomainException) {
                    $form->addError(new FormError($ex->getMessage()));
                } else {
                    throw $ex;
                }
            }
        }

        return null;
    }

    protected function extractRootCauseException(Throwable $ex): Throwable
    {
        $domainViolation = null;

        switch (true) {
            case $ex instanceof DomainException:
                $domainViolation = $ex;
                break;
            case $ex instanceof HandlerFailedException:
                $domainExceptions = $ex->getNestedExceptionOfClass(DomainException::class);
                if (\count($domainExceptions) > 0) {
                    $domainViolation = \reset($domainExceptions);
                }
                break;
        }

        return $domainViolation ?: $ex;
    }
}
