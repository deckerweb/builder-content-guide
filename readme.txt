=== Builder Content Guide ===
Contributors: deckerweb
Tags: content, patterns, templates
Requires at least: 7.0
Tested up to: 7.1.3
Requires PHP: 8.0
Stable tag: 1.0.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Curated instructions for everyday website tasks: find the original content, understand changes and open its editor.

== Description ==

Version 1.0.0 · Release candidate / Release-Kandidat · WordPress 7.0+ · PHP 8.0+

* Compact task list with search by name, purpose and original title, an area filter and a detail panel. Direct admin submenus provide access to finding content, managing the guide and adding entries.
* WordPress pages, posts, template parts, block navigation and classic menus; patterns, active Elementor Free/Pro documents, Bricks templates and GeneratePress Elements.
* Manual effect and usage explanations, editing guidance and deliberately enabled reader visibility.
* Separate reader access and management permissions. Editor links require existing original and builder permissions.
* Missing, trashed and disabled sources keep their references and have no active editor link.
* Reader help explains supported sources and shows an optional, administrator-maintained support contact. Extra search terms make everyday tasks easier to find.
* Search/filter originals by provider and content type; add or find guides at originals, open checked example pages, follow ordered steps, copy direct links and duplicate guides as hidden drafts.
* Adapt the top-level admin menu name and icon per website, for example Instructions. Leave the name empty to restore the translated default Find content.

The guide works locally and has no cloud or AI service dependency. Bundled deckerweb Library 0.8.1 offers an optional plugin catalog; its online catalog starts disabled. The bundled deckerweb Updater 2.1.0 uses GitHub through the native WordPress update workflow. GitHub receives standard WordPress HTTP request metadata and the public repository path. No guide entries or original content are sent. Source code is available at https://github.com/deckerweb/builder-content-guide. No stable release has been published yet.

== Installation ==

1. Install the supplied plugin ZIP on a staging website and activate Builder Content Guide.
2. Open Find content → Manage guide as a website administrator.
3. Pick a few useful tasks, such as renaming a menu item, updating the footer phone number or maintaining a shared contact template.
4. Add a guide entry, choose an original and describe name, purpose, area, effects, usage and editing guidance.
5. Enable visibility deliberately. Under Guide access, explicitly select the roles that may read visible entries.
6. Try three actual maintenance tasks with an editor before using the guide for a client handover.
7. Menu paths use the default name Find content; a custom menu name replaces that entry point on your website.

== Frequently Asked Questions ==

= Does every content item need a guide? =

No. Focus on typical problem cases, common stumbling blocks and frequently edited templates. A few clear instructions can be more useful than a complete catalog.

= Does the guide copy templates? =

No. It stores original references and explanations. Original content remains in its own system.

= Does visibility grant editing permission? =

No. Reader access only reveals curated explanations. WordPress object permissions and Bricks builder permissions still control editing.

= Are usage and effects detected automatically? =

No. The administrator describes them manually. Unknown effects are labeled Check usage. No automatic usage counts are shown.

= What if an original disappears? =

Its reference and last known title remain. The administrator sees a review notice and readers have no active edit action.

= Does it work on Multisite? =

Guide entries and role grants belong to each website. Network activation is supported; new sites start with an empty guide and no role grants.

= What happens when the plugin is removed? =

Guide entries and access settings remain. The updater cache is removed. The embedded Library follows its shared last-host cleanup rules and preserves data by default.

== Changelog ==

= 1.0.0 =

* New: Curated content guide for WordPress patterns, Bricks templates and GeneratePress elements.
* New: Reader help with supported content types and an optional support contact.
* New: WordPress pages, posts, template parts, block navigation, classic menus and active Elementor Free/Pro document types.
* New: Searchable original selection, related guide links, example pages, ordered editing steps, copyable direct links and duplication as invisible drafts.
* New: Customizable site admin menu name and bundled WordPress icon.
* Improved: Direct admin submenus for finding content, managing the guide and adding entries.
* Improved: Additional search terms, help after unsuccessful searches and reminders before editing shared building blocks.

Actual PHP 8.0 execution, a licensed Bricks editing workflow, real GP Premium Elements and a three-task client pilot still require acceptance testing. This package is a release candidate; publication follows completion of the outstanding acceptance checks. Tested with Elementor Free 4.3.4 and Elementor Pro 4.3.1. Inactive document types are unavailable; clipboard failure falls back to selecting the link for manual copying.
