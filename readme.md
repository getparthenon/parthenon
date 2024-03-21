<p align="center">
  <img width="450px" src="https://getparthenon.com/images/logo.svg">
</p>

<p align="center">
  <a href="https://scrutinizer-ci.com/g/getparthenon/parthenon/?branch=main">
    <img src="https://scrutinizer-ci.com/g/getparthenon/parthenon/badges/quality-score.png?b=main">
  </a>
  <a href="https://packagist.org/packages/parthenon/parthenon">
    <img alt="Packagist Downloads" src="https://img.shields.io/packagist/dt/parthenon/parthenon">
  </a>
  <br>
</p>

## What is Parthenon? 

[Parthenon](https://getparthenon.com) is a general functionality bundle for Symfony. Combined with the skeleton application it is a SaaS boilerplate which can allow you to build a project quicker by providing the generic functionality you would require.

### Project Goals

* Provide a solid foundation to build upon
* Make building Symfony applications faster and enjoyable
* Provide generic functionality that can be used to support custom business logic
* Provide bug fixes, performance fixes, and documentation fixes.
* Allow developers to focus on custom business logic instead of generic functionality.

### Project Non-goals

* Supporting other frameworks or languages
* Provide custom business logic

## Features

* User System
* Admin System - Athena
* Notification Support - (Email & Slack)
* AB Testing
* Multi-Tenancy
* Payments Support Via Stripe
* Multi-step forms - Funnel
* SaaS Subscription/Plans
* Invoice

## Getting started

To add Parthenon to a pre-existing Symfony application, just run the composer command:

```sh
composer require parthenon/parthenon
```

Or you can use the [Parthenon Skeleton Application](https://github.com/parthenon/skeleton) that includes a VueJS frontend.

```sh
composer create-project parthenon/skeleton
```

You can check out the skeleton application docs at https://getparthenon.com/docs/skeleton/overview or find the repository at https://github.com/getparthenon/skeleton

## Documentation

The documentation can be found on the Parthenon website https://getparthenon.com/docs/getting-started/.

If you wish to contribute to the documentation. Or just look at the raw files. They can be found at https://github.com/getparthenon/parthenon-docs.

## Support

Support is provided via GitHub, Slack, and Email.

If you have a commercial license you will be able to list the GitHub accounts that you want to link to the license. This
means when an issue is created by an account linked to a commercial license they will get priority support. All other
issues will be given best effort support.

* Github: You can make an issue on [getparthenon/monorepo](https://github.com/getparthenon/monorepo/issues/new/choose)
* Email: support@getparthenon.com
* Slack: [Click here](https://join.slack.com/t/parthenonsupport/shared_invite/zt-1gujl7xsw-OALGFlPs~_Vf1cw6zaEkdg) to signup

Issues we will provide support and fixes for:

* Defects/Bugs
* Performance issues
* Documentation fixes/improvements
* Lack of flexibility
* Feature requests

## FAQ

### Is Parthenon Open-Source?

Yes, it's released under GPL v3.

### Can I use Parthenon for free?

Yes.

### Who is Parthenon for?

Parthenon is for people who want to operate a web company that doesn't focus on the boring tech that everyone has done.

From bootstraps that want to start their business on the right footing to companies that want to improve their tech to large companies that have new projects and don't want to rebuild the same features they've done so many times.

### Can I use Parthenon with my existing Symfony application?

Yes. Parthenon is a bundle that can be used with your existing Symfony application. All the modules are toggable. So if you only want to use one part, you can.

### Will I be able to grow with Parthenon?

Parthenon is designed to scale. It has been purposefully designed so that things are able to be replaced as you grow.

We know that as your system scales, there will be parts of Parthenon you'll want to replace with highly custom code, and we designed Parthenon to allow you to do it with ease.