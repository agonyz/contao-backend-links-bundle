<?php

declare(strict_types=1);

/*
 * This file is part of agonyz/contao-backend-links-bundle.
 *
 * (c) agonyz
 *
 * @license LGPL-3.0-or-later
 */

namespace Agonyz\ContaoBackendLinksBundle\EventListener;

use Contao\CoreBundle\Csrf\ContaoCsrfTokenManager;
use Contao\CoreBundle\Event\MenuEvent;
use Contao\NewsBundle\Security\ContaoNewsPermissions;
use Symfony\Component\Security\Core\Security;
use Terminal42\ServiceAnnotationBundle\Annotation\ServiceTag;

/**
 * @ServiceTag("kernel.event_listener", event="contao.backend_menu_build", priority=-255)
 */
class BackendMenuListener
{
    private $security;
    private $contaoCsrfTokenManager;

    public function __construct(Security $security, ContaoCsrfTokenManager $contaoCsrfTokenManager)
    {
        $this->security = $security;
        $this->contaoCsrfTokenManager = $contaoCsrfTokenManager;
    }

    public function __invoke(MenuEvent $event): void
    {
        $tree = $event->getTree();

        if ('mainMenu' !== $tree->getName()) {
            return;
        }

        // show the custom backend menu entry
        $this->addBackendMenuEntry($event);

        // get the csrf token
        $csrfToken = $this->contaoCsrfTokenManager->getDefaultTokenValue();

        // show the node if the user can edit news archives
        if ($this->security->isGranted(ContaoNewsPermissions::USER_CAN_EDIT_ARCHIVE)) {
            $this->addImportantLinkNode($event, $csrfToken);
        }
    }

    private function addBackendMenuEntry(MenuEvent $event): void
    {
        $tree = $event->getTree();

        $agonyzNode = $event->getFactory()
            ->createItem('agonyz-backend-links-menu-entry')
            ->setChildrenAttribute('id', 'agonyz-backend-links-menu-entry')
            ->setUri('/contao?mtg=agonyz-backend-links-menu-entry')
            ->setLinkAttribute('onclick', "return AjaxRequest.toggleNavigation(this, 'agonyz-backend-links-menu-entry', '/contao')")
            ->setLinkAttribute('class', 'group-agonyz-backend-links')
            ->setLabel('agonyz-backend-links')
        ;

        $tree->addChild($agonyzNode);
    }

    private function addImportantLinkNode(MenuEvent $event, string $csrfToken): void
    {
        $tree = $event->getTree();
        $parentNode = $tree->getChild('agonyz-backend-links-menu-entry');

        $childNode = $event->getFactory()
            ->createItem('agonyz-important-link')
            ->setUri('/contao?do=news&table=tl_news&id=1&rt='.$csrfToken)
            ->setLabel('Important Link 1')
            ->setLinkAttribute('class', 'agonyz-important-link')
        ;

        $parentNode->addChild($childNode);
    }
}
