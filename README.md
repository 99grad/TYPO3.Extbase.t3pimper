# t3pimper
Erweitert das Typo3 Front- und Backend um viele nützliche Funktionen, die wir immer wieder vermisst haben.

Feature-List:

#### Backend: Typo3 Seitenbaum
+ "Im Menü verbergen / sichtbar machen" direkt im Kontextmenü
+ "Seite kopieren / einfügen" in erster Hierarchie des Kontextmenüs
+ Navigations-Titel statt Seitentitel im Seitenbaum anzeigen
+ "Kopie von" entfernen, wenn Seite kopiert wird

#### Backend: Inhaltselemente
+ Mehrzeilige Überschriften
+ Feld "Untertitel" bei Inhaltselementen anzeigen
+ Neue Felder "Farbe" und "Schmuck" bei Headlines
+ Neues Feld "Abstand zum Rand" im Reiter "Erscheinungsbild"
+ Neue Felder für Abstand oben, rechts, unten, links im Reiter "Erscheinungsbild"
+ Erlaubt auch negativen Margin bei den Angaben für Abstand
+ Neues Feld "Drehung" im Reiter Erscheinungsbild
+ Upload-Felder für fileadmin direkt im Seitenbaum-PopUp zeigen
+ "Kopie von" entfernen, wenn Inhaltselemente kopiert werden

#### Backend/Frontend: "Rahmen"
+ Starke Vereinfachung des Anlegen von neuen Rahmen durch generischen Ansatz

#### Backend: User
+ "Alle Caches löschen" im Typo3-Blitz zeigen

#### Frontend: Markup
+ Breadcrumb aus pids als class-Attribut an body-Tag hängen
+ Language-ID als class-Attribut an body-Tag
+ Bessere class-Attribute bei `hgroup` und `h..`-Elementen für Ausrichtung, Farbe, Stil und Schmuck einer Headline

#### Frontend: Lightboxes
+ Bilder für jQuery-Lightboxen (shaded lightbox fancybox) vorbereiten, wenn "Bei Klick vergrößern" im Inhaltselement gesetzt ist



---

# Headline-Typen, Farben und Schmuck
Normalerweise müssen die Einstellungen für Headline-Typen, Rahmen und Einrückungen etc. an zwei Stellen im TypoScript definiert werden: In der `page TSconfig` wird das Label und die Auswahl-Optionen für die Dropdown-Menüs im Backend definiert und im TS-Setup/Seiten-Template die Ausgabe im HTML gesteuert über z.B. `tt_content.stdWrap.innerWrap.cObject...`.

Um das zu vereinfachen, verfolgt t3pimper einen generischen Ansatz: Es genügt, im `page TSconfig` die Auswahl-Optionen der Dropdowns zu definieren. Die Headlines bzw. Inhaltselemente werden dann automatisch mit generischen, numerierten Klassen bestückt.

Zusätzlich war uns das Dropdown "Headline-Typen" bei vielen Projekten zu wenig – wenn man eine h1 und h2 in zwei Farben und optional mit und ohne Linie darunter festlegen wollte, dann brauchte man bereits 8 Varianten im Dropdown. Besser (vorallem in Bezug auf die CSS-Definitionen) fanden wir eine Trennung in Farbe, Stil und Schmuck der Headline. Die h1-h6-Tags und ´hgroup`-Tags bekommen diese Klassen mitgeliefert, z.B.

```html
<hgroup class="grcenter grh-10 grdeco-12 grcol-11">
   <h1 class="center h-10 deco-12 col-11">Das ist die Headline</h1>
   <h2 class="subheader">Und das die Subhead</h2>
</hgroup>
```

[Weitere Informationen](http://labor.99grad.de/2015/01/14/t3pimper-kurzdoku/)

#### Headline-Typen
Neue Typen können mit `addItems` hinzugefügt werden. Die Nummer der Headline bestimmt dabei die Hierarchie und Klasse. “31″ wird z.B. zu einer h3 mit der Klasse “h-31″, “44″ wäre h4 mit class=”44″ etc. Eine weitere Formatierung im TS-Setup (lib.stdheader.10…) entfällt.

```
# kommt ins Page TSconfig:

########################################################
# Optionen für "Headline-Typ"

TCEFORM.tt_content.header_layout.altLabels {
   0 = Standard
   2 = kleinere Überschrift
   100 = Nicht anzeigen
}

TCEFORM.tt_content.header_layout.addItems {
	10 = Groß und fett
	21 = klein und fett
	22 = gross und versal
}

TCEFORM.tt_content.header_layout.removeItems = 1,4,5,6,7,8,9
```

#### Headline-Farben
Die Farbe erscheint als `class="col-xx"` an dem H-Tag. "xx" ist dabei der Key im Typoscript. Mit removeItems, altLabels und addItems kann das Dropdown geändert werden:
```
# kommt ins Page TSconfig:

TCEFORM.tt_content.tx_t3pimper_headercolor {
   removeItems = 2,3,4,5,6
   altLabels {
      1 = Umbra
   }
   addItems {
      10 = apeshit-brown
   }
}
```

#### Headline-Schmuck
Der Schmuck erscheint als `class="deco-xx"´ an dem H-Tag. “xx” ist dabei der Key im Typoscript. Mit removeItems, altLabels und addItems kann das Dropdown geändert werden:
```
# kommt ins Page TSconfig:

TCEFORM.tt_content.tx_t3pimper_headerdeco {
   removeItems = 2,3,4,5
   altLabels {
      1 = Line drunter
   }
   addItems {
      10 = Super!
   }
}
```

## Styling Rahmen / Layout
#### Hinzufügen/Ändern von Layout-Optionen
Das Layout erscheint als `class="layout-xx"´ an dem Inhaltselement-DIV. “xx” ist dabei der Key im Typoscript:
```
# kommt ins Page TSconfig:

TCEFORM.tt_content.layout {
   removeItems = 2,3,4,5,6
   altLabels {
      1 = Kleinere Schrift
   }
   addItems {
      10 = Größer Zoomen
   }
}
```

#### Hinzufügen/Ändern von Rahmen/Einrückungen-Optionen
Der gewählte Rahmen erscheint als `class="rahmen-xx"` an dem Inhaltselement-DIV. “xx” ist dabei der Key im Typoscript:
```
# kommt ins Page TSconfig:

TCEFORM.tt_content.section_frame {
   removeItems = 1,5,6,10,11,12,20,21
   addItems {
      100 = Grauer Hintergrund
      101 = Blaue Blumen
   }
}
```

#### Hinzufügen/Ändern von “Abstand zum Rand”
Der “Abstand zum Rand” erscheint als `class="marg-xx"´ an dem Inhaltselement-DIV. “xx” ist dabei der Key im Typoscript.
```
# kommt ins Page TSconfig:

TCEFORM.tt_content.tx_t3pimper_margin {
   # So kann man das Dropdown komplett ausblenden:
   # disabled = 1
   removeItems = 2,3,4,5,6
   altLabels {
      1 = viel Abstand
   }
   addItems {
      10 = super viel Abstand
   }
}
```

## Breadcrumb als Klasse an den body-Tag
Ergänzt den body-Tag um Klassen für die pid bis zur aktuellen Seite und eine Klasse für die uid der aktuellen Sprache im Frontend:

```html
<body class="rpid-0 rpid-2 rpid-3 lang-0">
...
</body>
```
