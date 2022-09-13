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

**Version 2.0 is still WIP. Release date is expected to be 03.10.2022**

## What is Parthenon? 

Parthenon is a general functionality bundle for Symfony. Combined with the skeleton application it is a SaaS boilerplate which can allow you to build a project quicker by providing the generic functionality you would require.

### Project Goals

* Provide a solid foundation to build upon
* Make building Symfony applications faster and make enjoyable
* Provide generic functionality that can be used to support custom business logic
* Provide bug fixes, performance fixes, and documentaion fixes.

### Project Non-goals

* Supporting other frameworks or languages
* Provide custom business logic
* Provide a FOSS solution

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

To add Parthenon to a pre-existing Symfony application, just run the composer command

```sh
composer require parthenon/parthenon
```

Or you can use the Parthenon Skeleton Application that includes a VueJS frontend.

```sh
composer create-project parthenon/skeleton
```

You can check out the skeleton application docs at https://getparthenon.com/docs/skeleton/overview or find the repository at https://github.com/getparthenon/skeleton

## FAQ

### Is Parthenon Open-Source?

Parthenon is not considered to be open-source; it's made available under a license type known as a source available license. The License Parthenon is released under is the Business Source License which was created by MariaDB. The premise is you're allowed to use Parthenon free of charge for non-production use but are required to have a commercial license if you use it in production and generate more than $5,000 USD a month.

### Can I use Parthenon for free?

A quick answer is that you can use Parthenon for free if you're generating less $5,000 USD a month.

For non-production use, you can use Parthenon for free. This includes development and testing. If you're generating less than $5,000 USD a month in revenue you can use Parthenon for free. Otherwise, you'll need a commercial license to use it in production.

### Where can I get a commercial license?

You can find out more information about getting a commercial license at https://getparthenon.com.

### How much does a commercial license cost?

The cost of a commercial license is $250 per developer.

### Do I get a perpetual commercial license for Parthenon?

When you get a license, you have a perpetual license for the minor version of Parthenon that is currently released at the time of purchase, as well as the right to receive updates for a year. If you subscribe to the one-year update support, you'll be able to always use the latest version. If you choose not to continue an updates subscription, you'll be entitled to use the minor version of Parthenon that was available at the last yearly license update.

For example, if you buy a commercial license and the minor version is 2.0, and you don't subscribe to a yearly subscription, you'll be able to use 2.0 and any minor version released throughout the year, but once the update support has ended, you'll be entitled to use 2.0 only.

Another example: if you buy a commercial license and the minor version is 2.0 and don't subscribe for three years, and after the support license has ended, you would be entitled to use the minor version that was available 12 months before. So if 2.4 was released at the start of the final year of update support and 2.5 was available at the end of it, you would be entitled to use version 2.4 after the support license is over.

### Who is Parthenon for?

Parthenon is for people who want to operate a web company that doesn't focus on the boring tech that everyone has done.

From bootstraps that want to start their business on the right footing to companies that want to improve their tech to large companies that have new projects and don't want to rebuild the same features they've done so many times.

### Can I use Parthenon with my existing Symfony application?

Yes. Parthenon is a bundle that can be used with your existing Symfony application. All the modules are toggable. So if you only want to use one part, you can.

### Will I be able to grow with Parthenon?

Parthenon is designed to scale. It has been purposefully designed so that things are able to be replaced as you grow.

We know that as your system scales, there will be parts of Parthenon you'll want to replace with highly custom code, and we designed Parthenon to allow you to do it with ease.

### What happens if the development of Parthenon stops?

Because we've licensed Parthenon under the Business Source License, after three years, the release will be licensed under a FOSS license which means the community will be able to take over if anything unforeseen happens.
