# Welcome to the GCMS documentation page!

<section id="doc">

<section id="intro">

## Things to know

GCMS is a Symfony 4 installation with some GUI for content managers and developers, so if you can do something with Symfony, you can do it with GCMS by coding directly within the source. It also means that you can extend/modify the behavior of the backoffice by yourself.

</section>

<section id="tableofcontents">

## Table of contents

[The config tab](#config_tab)

[- Routes](#routes_tab)

[- Taxonomy](#taxonomy_tab)

[- Clear cache](#cache_tab)

[- Logs](#logs_tab)

[- Site's config](#siteconf_tab)

[- Database's config](#dbconf_tab)

[The pages tab](#pages_tab)

[The entries tab](#entries_tab)

[The components tab](#components_tab)

[The files tab](#files_tab)

[The users tab](#users_tab)

[The documentation tab](#doc_tab)

[Going further](#more)

</section>

<section id="config_tab">

# Config

<section id="routes_tab">

## Routes

Here, you can see the routes of your pages and content types and the code to include in your templates to call a specific route.

To get the ID of an entry you should look at the list of entries for the corresponding content type, it's the first data in the line.

</section>

<section id="taxonomy_tab">

## Taxonomy

Here, you can manage the groups of taxonomy and the members of these groups, you can later link a page or a content type to one or several taxonomy group(s).

</section>

<section id="cache_tab">

## Clear cache

Execute the cache:clear command of Symfony.

</section>

<section id="logs_tab">

## Logs

Here are displayed the 100 last lines of the log file, the latest logs are the ones on the top of the page.

</section>

<section id="siteconf_tab">

## Site config

Here, you can update the data you entered when installing GCMS, site's name, slogan and locale.

If you want to update the logo and the favicon you should replace the files within "public/assets/site_config/" without changing their names.

</section>

<section id="dbconf_tab">

## Database config

Here, you can update the .env file of symfony for the database access.

</section>

</section>

<section id="pages_tab">

## Pages

Here, you can create, edit, delete, publish and unpublish pages.

When creating a page you should go through all the process until you've been redirected to the pages' list, if not, the creation won't be complete and some bugs may occur. If this is the case, you should :

*   Delete the route of the page within "src/Controller/PageController.php"
*   Delete the entity file of the page within "src/Entity/"
*   Delete the form file of the page within "src/Form/"
*   Delete the repository file of the page within "src/Repository/"
*   Delete the template file of the page within "templates/theme/pages/"

The edition of existing pages cannot cause such bugs, so don't worry.

</section>

<section id="entries_tab">

## Entries

Here, you can create and delete content types, and then create, edit, delete, publish and unpublish entries within a content type.

When creating a content type you should go through all the process until you've been redirected to the content types' list, if not, the creation won't be complete and some bugs may occur, if this is the case, you should :

*   Delete the entity file of the content type within "src/Entity/"
*   Delete the form file of the content type within "src/Form/"
*   Delete the repository file of the content type within "src/Repository/"
*   Delete the template folder of the page within "templates/theme/entries/"

</section>

<section id="components_tab">

## Components

Here, you can list the components available in your website and their inclusion path.

Inclusion path is {{'{{'}} render(controller('App\\Controller\\ComponentsController::{function}')) {{'}}'}}

</section>

<section id="files_tab">

## Files

Here are displayed the files uploaded within the "public/assets/images/" directory, by default it's made for your pages' and content types' images but anything you upload into that directory will be displayed and can be deleted.

</section>

<section id="users_tab">

## Users

Here you can add, edit and delete user accounts to access the backoffice, there are several roles :

*   Editor, can add entries and edit the ones he created.
*   Redactor, can add content types, pages and edit the ones he created.
*   Publisher (standalone role), can publish and unpublish content, the range of contents he can act on is defined by his main role (Useful only for Editors and Redactors as the next roles in the hierarchy have it by default).
*   Supervisor, full control over content types, entries and pages.
*   Manager, can manage files, taxonomy, clear the cache and display routes and logs.
*   Admin, as you might expect, can do anything.
*   Developer (standalone role), can view components.

The roles not marked with the "(standalone role)" are hierarchicals, so the lowest role is Editor and the highest is Admin, everyone can be Developer and the Publisher role is meaningful only for Editors and Redactors because it is part of every higher roles.

</section>

<section id="doc_tab">

## Documentation

I wonder what you were expecting here.

Let's make some websites ! ;)

</section>

<section id="more">

## Going further

In order to deploy your application you must have set a homepage URL for one page, the homepage will be accessible at the root of your server/directory, for example :

*   https://mydomain/ for a production server.
*   https://localhost:8000/ for a Symfony 4 Built-in server.
*   http://localhost/gcms/ for an apache server.

Then, it's up to you to make the linking between your pages directly within the "templates/theme/" folder. See the routes' and components' tabs to learn how to make these links.

A link to the Symfony Documentation may be useful, so here it is !

[Symfony Doc](https://symfony.com/doc/4.0//index.html#gsc.tab=0)

</section>

</section>