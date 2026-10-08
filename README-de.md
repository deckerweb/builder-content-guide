# Builder Content Guide

![Builder Content Guide](graphics/github-de-1280x640.png?v=c9c6f52a68468159b35908fb092cc5900ace083c)

## Kurzvorstellung

Finde den passenden Website-Baustein, verstehe die Wirkung einer Änderung und öffne den Original-Editor. Die Website-Betreuung kuratiert eine kleine Übergabeübersicht; Inhalte bleiben in WordPress und seinen aktiven Buildern. Nicht jeder Inhalt braucht einen Guide. Starte mit häufigen Pflegeaufgaben, typischen Stolpersteinen und gemeinsam verwendeten Vorlagen, deren Änderungen mehrere Stellen betreffen.

Version 1.0.0 · Stable Release · WordPress 7.0+ · PHP 8.0+

[Nutzungsanleitung](docs/usage-de.md) · [Fragen nach Themen](docs/FAQ-de.md) · [Dokumentation](https://deckerweb.github.io/builder-content-guide/index-de.html) · [English](README.md)

## Inhaltsverzeichnis

- [Auf einen Blick](#auf-einen-blick)
- [Erste Schritte](#erste-schritte)
- [Funktionen](#funktionen)
- [Aufgaben aus dem Alltag](#aufgaben-aus-dem-alltag)
- [Häufige Fragen](#häufige-fragen)
- [Daten und Lebenszyklus](#daten-und-lebenszyklus)
- [Optionale Online-Dienste](#optionale-online-dienste)
- [Prüfstand](#prüfstand)
- [Änderungsverlauf](#änderungsverlauf)
- [Über den Entwickler](#über-den-entwickler)
- [Fehler melden und Hilfe bekommen](#fehler-melden-und-hilfe-bekommen)
- [Entwicklung unterstützen](#entwicklung-unterstützen)
- [Lizenz und eingebettete Komponenten](#lizenz-und-eingebettete-komponenten)

## Auf einen Blick

- Alltagsaufgaben über Suche und Website-Bereiche finden.
- Den Original-Editor mit vorhandenen Bearbeitungsrechten öffnen.
- Verwendung, Wirkung und nummerierte Arbeitsschritte erklären.
- WordPress-Inhalte, Navigation und aktive Builder-Vorlagen zuordnen.
- Leserhilfe und einen optionalen Ansprechpartner bereitstellen.
- Leserrollen sowie Namen und Icon des Admin-Menüs anpassen.
- Guide-Links kopieren und Anleitungen als versteckte Entwürfe duplizieren.

## Erste Schritte

1. Installiere das mitgelieferte Plugin-ZIP auf einer Testwebsite und aktiviere Builder Content Guide.
2. Öffne als Website-Administrator Inhalte finden → Guide pflegen.
3. Wähle zunächst wenige hilfreiche Aufgaben, etwa einen Menüpunkt umbenennen, die Telefonnummer im Footer ändern oder eine gemeinsame Kontaktvorlage pflegen.
4. Lege einen Guide-Eintrag an, wähle das Original und beschreibe Name, Zweck, Bereich, Wirkung, Verwendung und Bearbeitungshinweis.
5. Aktiviere die Sichtbarkeit bewusst. Wähle unter Guide-Zugriff ausdrücklich die Rollen, die sichtbare Einträge lesen dürfen.
6. Erprobe vor der Kundenübergabe drei reale Pflegeaufgaben mit einem Redakteur.
7. Die Menüpfade verwenden den Standardnamen Inhalte finden; ein eigener Menüname ersetzt diesen Einstieg auf deiner Website.

## Funktionen

### Aufgabenliste

Kompakte Aufgabenliste mit Suche nach Name, Zweck und Originalname, Bereichsfilter und Detailansicht. Direkte Admin-Untermenüs führen zur Inhaltssuche, Guide-Pflege und zum Hinzufügen von Einträgen.

### Unterstützte Quellen

WordPress-Seiten, Beiträge, Template-Teile, Block-Navigation und klassische Menüs; Patterns, aktive Elementor-Free-/Pro-Dokumente, Bricks-Templates und GeneratePress-Elements.

### Manuelle Erklärungen

Manuell beschriebene Wirkung und Verwendung, Bearbeitungshinweise und bewusst aktivierte Sichtbarkeit.

### Berechtigungen

Getrennte Rechte zum Lesen und Pflegen. Bearbeitungslinks erfordern vorhandene Rechte am Original und im Builder.

### Fehlende Originale

Fehlende, gelöschte oder deaktivierte Quellen behalten ihre Referenz und erhalten keinen aktiven Bearbeitungslink.

### Leserhilfe und Unterstützung

Eine Leserhilfe erklärt die unterstützten Quellen und zeigt einen optionalen, von der Betreuung gepflegten Ansprechpartner. Zusätzliche Suchbegriffe erleichtern die Suche nach Alltagsaufgaben.

### Einfacher Arbeitsablauf

Originale nach Provider und Inhaltstyp durchsuchen; Guides am Original hinzufügen oder finden, geprüfte Beispielseiten öffnen, geordnete Arbeitsschritte lesen, Direktlinks kopieren und Guides als ausgeblendete Entwürfe duplizieren.

### Menü-Integration

Name und Icon des obersten Admin-Menüs je Website anpassen, etwa Anleitung. Ein leerer Name stellt den übersetzten Standard Inhalte finden wieder her.

## Aufgaben aus dem Alltag

![Demonstrationswebsite mit Kontakttext, Menüpunkt und Footer-Telefonnummer](docs/images/everyday-tasks-de.jpg)

Die Demonstrationsansicht zeigt drei Alltagsaufgaben: Kontakttext ändern, Menüpunkt umbenennen und Telefonnummer im Footer ändern. Das oberste Menü wurde hier Anleitung genannt.

[Nutzungsanleitung](docs/usage-de.md)

## Häufige Fragen

### Braucht jeder Inhalt einen Guide?

Nein. Konzentriere dich auf typische Problemfälle, Stolpersteine und häufig bearbeitete Vorlagen. Wenige verständliche Anleitungen können hilfreicher sein als ein vollständiger Katalog.

### Kopiert der Guide Templates?

Nein. Er speichert Originalreferenzen und Erklärungen. Die Originalinhalte bleiben im jeweiligen System.

### Erteilt Sichtbarkeit Bearbeitungsrechte?

Nein. Der Lesezugriff zeigt nur kuratierte Erklärungen. Die WordPress-Objektrechte und der Bricks-Builder-Zugang bestimmen weiterhin die Bearbeitung.

### Werden Verwendung und Wirkung automatisch erkannt?

Nein. Die Betreuung beschreibt sie manuell. Bei unbekannter Wirkung erscheint Verwendung prüfen. Es werden keine automatischen Verwendungszahlen angezeigt.

### Was passiert, wenn ein Original fehlt?

Die Referenz und der zuletzt bekannte Name bleiben erhalten. Die Betreuung sieht einen Prüfhinweis; Leser erhalten keine aktive Bearbeitungsaktion.

### Funktioniert das Plugin in Multisite?

Guide-Einträge und Leserfreigaben gehören zur jeweiligen Website. Netzwerkaktivierung wird unterstützt; neue Websites beginnen mit leerem Guide und ohne Rollenfreigaben.

### Was passiert bei der Deinstallation?

Guide-Einträge und Leserfreigaben bleiben erhalten. Der Updater-Cache wird entfernt. Die eingebettete Library folgt ihren gemeinsamen Regeln zur Bereinigung beim letzten Host und erhält Daten standardmäßig.

[Fragen nach Themen](docs/FAQ-de.md)

## Daten und Lebenszyklus

Guide-Einträge, Leserzugriff, Ansprechpartner und Menüeinstellungen werden getrennt je Website gespeichert. Originalinhalte bleiben im jeweiligen System. Deaktivierung und Deinstallation erhalten Guide-Daten und Einstellungen. Bei der Deinstallation wird der Updater-Cache entfernt; die gemeinsame Library erhält ihre Daten standardmäßig.

## Optionale Online-Dienste

Der Guide funktioniert lokal und benötigt keinen Cloud- oder KI-Dienst. Die eingebettete deckerweb Library 0.8.1 bietet einen optionalen Plugin-Katalog; dessen Online-Modus ist zunächst ausgeschaltet. Der deckerweb Updater 2.1.0 nutzt GitHub im normalen WordPress-Updateablauf. GitHub erhält die üblichen WordPress-HTTP-Anfragedaten und den öffentlichen Repository-Pfad. Guide-Einträge und Originalinhalte werden nicht übertragen. Der Quellcode ist unter https://github.com/deckerweb/builder-content-guide verfügbar.

## Prüfstand

Laufzeitprüfungen: WordPress 7.0 mit PHP 8.1.29 und WordPress 7.1.3 mit PHP 8.4.5. PHP 8.0 wurde nicht getestet. Getestet mit Elementor Free 4.3.4 und Elementor Pro 4.3.1. Inaktive Dokumenttypen sind nicht verfügbar; bei fehlendem Zugriff auf die Zwischenablage wird der Link zum manuellen Kopieren markiert.

## Änderungsverlauf

### 1.0.0 — Stable Release (2026-10-08)

- **Neu:** Kuratierter Content Guide für WordPress-Patterns, Bricks-Templates und GeneratePress-Elements.
- **Neu:** Leserhilfe mit unterstützten Inhaltstypen und optionalem Ansprechpartner.
- **Neu:** WordPress-Seiten, Beiträge, Template-Teile, Block-Navigation, klassische Menüs und aktive Elementor-Free-/Pro-Inhaltstypen.
- **Neu:** Durchsuchbare Originalauswahl, Links zu zugehörigen Guides, Beispielseiten, geordnete Arbeitsschritte, kopierbare Direktlinks und Duplizieren als ausgeblendete Entwürfe.
- **Neu:** Anpassbarer Admin-Menüname und lokal bereitgestelltes WordPress-Icon je Website.
- **Verbessert:** Direkte Admin-Untermenüs zum Finden von Inhalten, Pflegen des Guides und Hinzufügen von Einträgen.
- **Verbessert:** Zusätzliche Suchbegriffe, Hilfe bei erfolgloser Suche und Hinweise vor der Bearbeitung gemeinsam verwendeter Bausteine.

## Über den Entwickler

Entwickelt und gepflegt von David Decker – DECKERWEB. Builder Content Guide konzentriert sich auf eine kleine redaktionelle Übergabeübersicht.

## Fehler melden und Hilfe bekommen

Melde Schwachstellen vertraulich über [GitHubs private Sicherheitsmeldungen](https://github.com/deckerweb/builder-content-guide/security/advisories/new). Veröffentliche ungepatchte Sicherheitsdetails nicht in öffentlichen Issues. Nutze für reproduzierbare Fehler ohne Sicherheitsbezug [GitHub Issues](https://github.com/deckerweb/builder-content-guide/issues).

Für Pflegeaufgaben auf deiner Website nutze den von der Betreuung hinterlegten Ansprechpartner.

## Entwicklung unterstützen

[Ko-fi](https://ko-fi.com/deckerweb) · [Buy Me a Coffee](https://buymeacoffee.com/daveshine) · [PayPal](https://paypal.me/deckerweb)

## Lizenz und eingebettete Komponenten

Copyright © 2026 David Decker – DECKERWEB. GPL-2.0-or-later. Unverändert eingebettet: deckerweb Library 0.8.1 und deckerweb Updater 2.1.0 von David Decker, beide GPL-2.0-or-later. Quellen: https://github.com/deckerweb/deckerweb-plugin-library und https://github.com/deckerweb/deckerweb-updater. Kein Elementor-, Bricks- oder GP-Premium-Quellcode enthalten.
