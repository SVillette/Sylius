<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Behat\Page\Admin\Product;

use Behat\Mink\Session;
use Sylius\Behat\Page\Admin\Crud\IndexPage as CrudIndexPage;
use Sylius\Behat\Service\Accessor\TableAccessorInterface;
use Sylius\Behat\Service\Checker\ImageExistenceCheckerInterface;
use Sylius\Behat\Service\Helper\AutocompleteHelperInterface;
use Symfony\Component\Routing\RouterInterface;

class IndexPage extends CrudIndexPage implements IndexPageInterface
{
    public function __construct(
        Session $session,
        $minkParameters,
        RouterInterface $router,
        TableAccessorInterface $tableAccessor,
        string $routeName,
        protected ImageExistenceCheckerInterface $imageExistenceChecker,
        protected AutocompleteHelperInterface $autocompleteHelper,
    ) {
        parent::__construct($session, $minkParameters, $router, $tableAccessor, $routeName);
    }

    public function filter(): void
    {
        $this->getElement('filter')->press();
    }

    public function filterByTaxon(string $taxonName): void
    {
        $this->autocompleteHelper->selectByName(
            $this->getDriver(),
            $this->getElement('taxon_filter')->getXpath(),
            $taxonName,
        );

        $this->waitForFormUpdate();
    }

    public function filterByMainTaxon(string $taxonName): void
    {
        $this->autocompleteHelper->selectByName(
            $this->getDriver(),
            $this->getElement('main_taxon_filter')->getXpath(),
            $taxonName,
        );

        $this->waitForFormUpdate();
    }

    public function chooseChannelFilter(string $channelName): void
    {
        $this->getElement('channel_filter')->selectOption($channelName);
    }

    public function hasProductAccessibleImage(string $productCode): bool
    {
        $productRow = $this->getTableAccessor()->getRowWithFields($this->getElement('table'), ['code' => $productCode]);
        $imageUrl = $productRow->find('css', 'img')->getAttribute('src');

        return $this->imageExistenceChecker->doesImageWithUrlExist($imageUrl, 'sylius_admin_product_thumbnail');
    }

    public function showProductPage(string $productName): void
    {
        $tableAccessor = $this->getTableAccessor();
        $table = $this->getElement('table');
        $row = $tableAccessor->getRowWithFields($table, ['name' => $productName]);
        $field = $tableAccessor->getFieldFromRow($table, $row, 'actions');

        $field->find('css', '[data-test-show-action="Details"]')->click();
    }

    public function checkFirstProductHasDataAttribute(string $attributeName): bool
    {
        return $this->getElement('first_product')->find('css', sprintf('[%s]', $attributeName)) !== null;
    }

    public function checkLastProductHasDataAttribute(string $attributeName): bool
    {
        return $this->getElement('last_product')->find('css', sprintf('[%s]', $attributeName)) !== null;
    }

    /** @return array<string, string> */
    protected function getDefinedElements(): array
    {
        return array_merge(parent::getDefinedElements(), [
            'channel_filter' => '#criteria_channel',
            'enabled_filter' => '#criteria_enabled',
            'first_product' => '.table > tbody > tr:first-child',
            'last_product' => '.table > tbody > tr:last-child',
            'main_taxon_filter' => '#criteria_main_taxon',
            'taxon_filter' => '#criteria_taxon',
        ]);
    }
}
