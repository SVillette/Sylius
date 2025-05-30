# How to disable localized URLs?

\==============================

URLs in Sylius are localised, this means they contain the `/locale` prefix with the current locale. For example when the `English (United States)` locale is currently chosen in the channel, the URL of homepage will look like that `localhost:8000/en_US/`.

If you do not need localised URLs, this guide will help you to disable this feature.

**1.** Customise the application routing in the `config/routes/sylius_shop.yaml`.

Replace:

.. code-block:: yaml

```
# config/routes/sylius_shop.yaml

sylius_shop:
    resource: "@SyliusShopBundle/Resources/config/routing.yml"
    prefix: /{_locale}
    requirements:
        _locale: ^[A-Za-z]{2,4}(_([A-Za-z]{4}|[0-9]{3}))?(_([A-Za-z]{2}|[0-9]{3}))?$

sylius_shop_payum:
    resource: "@SyliusShopBundle/Resources/config/routing/payum.yml"

sylius_payment_notify:
    resource: "@SyliusPaymentBundle/Resources/config/routing/integrations/sylius.yaml"

sylius_shop_default_locale:
    path: /
    methods: [GET]
    defaults:
        _controller: sylius.controller.shop.locale_switch:switchAction
```

With:

.. code-block:: yaml

```
# config/routes/sylius_shop.yaml

sylius_shop:
    resource: "@SyliusShopBundle/Resources/config/routing.yml"

sylius_shop_payum:
    resource: "@SyliusShopBundle/Resources/config/routing/payum.yml"

sylius_payment_notify:
    resource: "@SyliusPaymentBundle/Resources/config/routing/integrations/sylius.yaml"
```

**2.** Customise SyliusShopBundle to use storage-based locale switching in the `config/packages/sylius_shop.yaml`.

Replace:

.. code-block:: yaml

```
# config/packages/sylius_shop.yaml

sylius_shop:
    product_grid:
        include_all_descendants: true
```

With:

.. code-block:: yaml

```
# config/packages/sylius_shop.yaml

sylius_shop:
    product_grid:
        include_all_descendants: true
    locale_switcher: storage
```
