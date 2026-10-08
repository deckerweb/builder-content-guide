# Builder Content Guide Nutzungsanleitung

[English](usage.md) · [Fragen nach Themen](FAQ-de.md)

Builder Content Guide hilft Mitarbeitern, den richtigen Bearbeitungsort zu finden und die Wirkung einer Änderung zu verstehen. Die Website-Betreuung hinterlegt dafür gezielt kurze Anleitungen.

**Nicht jeder Inhalt braucht einen Guide.** Beginne mit den häufigsten Pflegeaufgaben, typischen Stolpersteinen und gemeinsam verwendeten Vorlagen. Drei verständliche Anleitungen können bereits eine wertvolle Übergabe sein.

## Für Leser Inhalte finden und bearbeiten

1. Öffne im WordPress-Admin **Inhalte finden**. Der Betreiber kann diesen Menüpunkt zum Beispiel **Anleitung** nennen.
2. Suche nach deiner Aufgabe, etwa „Telefonnummer“, „Kontakt“ oder „Menü“. Wähle bei Bedarf einen Bereich.
3. Öffne den passenden Guide. Lies Zweck, Verwendung und Wirkung, bevor du etwas änderst.
4. Folge den nummerierten Schritten. **Original bearbeiten** öffnet den vorhandenen Editor, sofern deine Rechte und die aktive Quelle dies erlauben.
5. Speichere im Original-Editor. Prüfe das Ergebnis auf der Website; **Website ansehen** öffnet die hinterlegte Beispielseite in einem neuen Tab.

Der Guide ändert keine Originalinhalte und vergibt keine Bearbeitungsrechte. Ein kopierter Guide-Link funktioniert nur für angemeldete, freigegebene Leser mit Zugriff auf diesen sichtbaren Eintrag. Scheitert automatisches Kopieren, markiere den Link und kopiere ihn manuell.

## Drei Aufgaben aus dem Alltag

![Demonstrationswebsite mit Kontakttext, Menüpunkt und Footer-Telefonnummer](images/everyday-tasks-de.jpg)

Die Abbildung zeigt die drei Aufgaben aus der Testwebsite. Das oberste Menü heißt hier **Anleitung**; einzelne WordPress-Menütexte sind in dieser Testumgebung englisch. Namen und Bearbeitungsorte werden für jede echte Website von der Betreuung festgelegt.

### Kontakttext ändern

**Guide-Name:** Kontakttext ändern. **Original:** die Kontaktseite oder tatsächlich eingebundene Kontaktvorlage. **Suchbegriffe:** Kontakt, Adresse, Öffnungszeiten.

1. Im Guide prüfen, ob die Angaben nur auf der Kontaktseite oder an mehreren Stellen verwendet werden.
2. Original bearbeiten öffnen. Bei einer mit Elementor bearbeiteten Seite öffnet sich Elementor.
3. Den beschriebenen Textbereich wählen und nur die vereinbarten Angaben ändern.
4. Aktualisieren und Kontaktseite prüfen. Bei gemeinsam verwendeter Vorlage weitere betroffene Seiten kontrollieren.

Die Anleitung muss den tatsächlichen Textbereich nennen. Wird der Text aus einem dynamischen Feld geladen, kann der Bearbeitungsort ein anderer sein; die Betreuung sollte das ausdrücklich beschreiben.

### Menüpunkt umbenennen

**Guide-Name:** Menüpunkt umbenennen. **Original:** das tatsächlich angezeigte klassische Menü oder die Block-Navigation. **Suchbegriffe:** Menü, Navigation, Beschriftung.

1. Die Verwendung lesen: Hauptnavigation, mobile Navigation oder mehrere Menüs?
2. Original bearbeiten öffnen und den beschriebenen Menüpunkt auswählen.
3. Die Menübeschriftung ändern und speichern.
4. Navigation auf Desktop und Mobilgerät prüfen. Zieladresse und Funktion des Links kontrollieren.

Eine Menübeschriftung ist nicht automatisch der Seitentitel. Für klassische Menüs wird die Menüverwaltung verwendet; Block-Navigation wird im Website-Editor eines passenden Block-Themes bearbeitet.

### Telefonnummer im Footer ändern

**Guide-Name:** Telefonnummer im Footer ändern. **Original:** etwa ein Elementor-Pro-Footer oder ein WordPress-Template-Teil. **Suchbegriffe:** Telefonnummer, Telefon, Footer, Fußbereich.

1. Verwendung und Wirkung lesen. Ein gemeinsamer Footer kann auf vielen Seiten erscheinen.
2. Original bearbeiten öffnen und den im Guide beschriebenen Telefonbereich auswählen.
3. Sichtbare Nummer ändern; bei einem Telefonlink auch dessen `tel:`-Ziel kontrollieren.
4. Speichern und die hinterlegte Beispielseite prüfen. Den Link auf einem Mobilgerät testen.

Der Guide erkennt nicht automatisch, wo ein Footer eingebunden ist. Die Betreuung beschreibt die tatsächliche Verwendung und hinterlegt eine geeignete Beispielseite.

## Für Administratoren Den Guide einrichten

Installiere und aktiviere das bereitgestellte Plugin-ZIP zuerst auf einer Testwebsite. Öffne **Inhalte finden → Guide pflegen**. Eine neue Installation enthält keine Anleitungen; die Demo-Einträge werden nicht mitinstalliert.

Wähle zunächst die drei wichtigsten Aufgaben. Unter **Guide-Eintrag hinzufügen** vergibst du einen verständlichen Aufgabennamen. Suche das Original nach Titel und filtere nach Provider oder Inhaltstyp. Filter ändern eine bestehende Zuordnung nicht stillschweigend; ohne JavaScript bleibt die native Auswahl verfügbar.

| Feld | Was du eintragen solltest |
| --- | --- |
| Name und Zweck | Die Aufgabe in Alltagssprache und das gewünschte Ergebnis |
| Original | Den wirklichen Bearbeitungsort, nicht bloß eine ähnlich benannte Vorlage |
| Bereich | Zum Beispiel Kontakt, Navigation oder Footer |
| Wirkung und Verwendung | Lokal, mehrere Stellen oder unbekannt; konkrete betroffene Bereiche |
| Bearbeitungshinweis | Grenzen, Stolpersteine und nötige Rücksprache |
| Schritte | Ein Schritt pro Zeile; die Leser sehen eine nummerierte Liste |
| Suchbegriffe | Wörter, nach denen Mitarbeiter wahrscheinlich suchen |
| Beispieladresse | Eine geprüfte vollständige HTTP-/HTTPS-Adresse zum Kontrollieren |
| Sichtbarkeit | Erst nach Prüfung ausdrücklich für Leser aktivieren |

Wähle unter **Guide-Zugriff** die Leserrollen ausdrücklich aus. Die Freigabe gilt je Website; sie ersetzt keine WordPress- oder Builder-Berechtigungen. Administratoren pflegen den Guide, freigegebene Leser lesen die sichtbaren Einträge.

### Ansprechpartner und Menü anpassen

Unter **Guide pflegen** kannst du einen optionalen Ansprechpartner mit Name, kurzer Erklärung, E-Mail, Telefon und Website hinterlegen. Aktiviere die Anzeige ausdrücklich. Leser sehen ihn auf **So findest du Inhalte** und als Hilfe bei erfolgloser Suche.

Unter **Admin-Menü** kannst du den obersten Menüeintrag umbenennen und eines von zehn WordPress-Icons auswählen. Ein leerer Name stellt **Inhalte finden** wieder her. Die Untermenüs und Rechte behalten ihre Aufgaben.

### Vorhandene Guides pflegen

Prüfe Guides nach Änderungen am Theme, Builder oder Original. **Duplizieren** kopiert nur die Anleitung und ihre Referenz als ausgeblendeten Entwurf. Passe Name, Original und Schritte an und aktiviere die Lesersichtbarkeit erst nach Prüfung. Das Original wird nicht kopiert.

An unterstützten Originalen können Administratoren **Guide-Eintrag hinzufügen** und **Zugehörige Guides** öffnen: in Inhaltslisten, im klassischen Editor, beim ausgewählten klassischen Menü sowie im Content-Guide-Bereich unterstützter Block- und Website-Editoren. Diese Einstiege dienen der Betreuung; sie sind keine automatische Guide-Einblendung für eingefügte Pattern-Kopien.

## Unterstützte Quellen und Grenzen

- WordPress-Seiten und Beiträge, Template-Teile des aktiven Themes, Block-Navigation und klassische Menüs.
- Gespeicherte und registrierte WordPress-Patterns. Eine eingefügte, unabhängige Pattern-Kopie bekommt keine automatische Verbindung zum Guide des Patterns.
- Aktive Elementor-Free-/Pro-Dokumenttypen. Globale Design-Kits werden nicht angeboten. Getestet mit Elementor Free 4.3.4 und Elementor Pro 4.3.1.
- Bricks-Templates mit vorhandenen Builder-Rechten und GeneratePress-Elements bei verfügbarer Quelle.

Unterstützung bedeutet nicht, dass für jeden Inhalt ein Guide existiert oder jeder Leser ihn bearbeiten darf. Inaktive Builder, fehlende Originale, Papierkorb und unpassende Themes können den Bearbeitungslink verhindern. Die gespeicherte Referenz bleibt erhalten.

## Wenn etwas fehlt

**Kein Guide gefunden:** Suche kürzer, wähle alle Bereiche und prüfe mit der Betreuung, ob ein sichtbarer Eintrag und eine Rollenfreigabe existieren. Auf der Hilfe-Seite werden unterstützte Inhaltstypen erklärt.

**Kein Bearbeitungslink:** Prüfe Original, aktive Quelle und vorhandene Bearbeitungsrechte. Die Leserfreigabe allein reicht nicht. Nach einem Theme-Wechsel kann Navigation oder ein Template-Teil einen anderen Bearbeitungsort haben.

**Pattern im Beitrag ohne Guide:** Eine Kopie ist ein eigenständiger Inhalt. Der Guide bleibt dem ursprünglichen Pattern zugeordnet; für eine wichtige Seitenaufgabe ist häufig ein Guide zur Seite sinnvoller.

**Direktlink nicht zugänglich:** Melde dich mit einem freigegebenen Konto an. Versteckte Entwürfe und nicht freigegebene Einträge bleiben verborgen.

## Übergabe und Daten

Erprobe Kontakttext, Menübeschriftung und Footer-Telefonnummer mit einem tatsächlich vorgesehenen Mitarbeiterkonto. Kontrolliere Verständlichkeit, Rechte, Bearbeitungsort und Ergebnis. Ergänze fehlende Hinweise gezielt.

Guide-Daten und Einstellungen bleiben bei Deaktivierung und Deinstallation erhalten. Im Multisite-Netzwerk besitzt jede Website ihren eigenen Guide, Leserzugriff, Ansprechpartner und Menüname. Neue Websites beginnen leer. Der Guide benötigt keinen Cloud-Dienst; Hinweise zu optionalen Online-Komponenten findest du in der Readme.
