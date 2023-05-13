<?php
namespace App\EventListener;

use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Contracts\Translation\TranslatorInterface;
use App\Service\CIBridgeService;

/**
 * Setup the locale at the kernel boot
 */
#[AsEventListener(event: RequestEvent::class, priority: 101)]
final class TranslationListener
{
    private CIBridgeService $ciBridge;
    private TranslatorInterface $translator;

    public function __construct(CIBridgeService $ciBridge, TranslatorInterface $translator) {
        $this->ciBridge = $ciBridge;
        $this->translator = $translator;
    }

    public function __invoke(RequestEvent $event): void
    {
        $request = $event->getRequest();
        $locale = $this->ciBridge->getLanguageCode();
        $this->translator->setLocale($locale);
        $request->setLocale($locale);
    }
}
