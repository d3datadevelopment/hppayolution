# D³ Data Development - Heidelpay Payolution
Installation und Schnellstart  
Stand: 2018-06-18  
Modulversion: 1.0.0.x  
Bearbeiter: KH  

## Mindestanforderungen
- PHP Version
  - 5.6.x bis 7.1.x
- PHP Encoder
  - installierter ionCube Loader
- Shopversion
  - OXID eShop Professional Edition in Version 6.x
  - oder OXID eShop Enterprise Edition in Version 6.x
- [D³-Modul-Connector](https://www.oxidmodule.com/Connector/) (kostenfrei bei D³ erhältlich) ab Version 5.1.0.0
- [D³-Heidelpay Premuim Edition](https://www.oxidmodule.com/OXID-eShop/Module/Heidelpay-Integrator-fuer-Oxid-PE.html) ab Version 6.0.1.0
- Installation via [Composer](https://getcomposer.org/)

## Wichtige Hinweise
##### Modulversion 1.0.0.0
Das Modul unterstütz derzeit nur das Flow-Theme Version v3.0.0

## Neuinstallation
##### 1. Paketquelle hinzufügen
Starten Sie die Konsole Ihres Webservers und wechseln in das Hauptverzeichnis Ihres
Shops (oberhalb des "source"- und "vendor"-Verzeichnisses). Senden Sie dort
diesen Befehl ab:  
```composer config repositories.D3modules composer https://satis.oxidmodule.com```  
Benötigt Ihre Installation einen anderen Aufruf von Composer, ändern Sie den Befehl
bitte entsprechend ab.

##### 2. Modul zur Installation hinzufügen
Führen Sie in der Konsole im selben Verzeichnis diesen Befehl aus, um Heidelpay Payolution
zur Installation hinzuzufügen:  
```composer require d3/heidelpay_payolution –-update-no-dev```  
Benötigt Ihre Installation einen anderen Aufruf von Composer, ändern Sie den Befehl
bitte entsprechend ab. 

Für weitere Optionen dieses Befehls lesen Sie bitte die
[Dokumentation von Composer](https://getcomposer.org/doc/03-cli.md#require).

##### 3. Providerspezifische Installation
Manche Provider erfordern besondere Einstellungen für installierte Module. Ob Ihr
Anbieter spezielle Anforderungen stellt und wie diese aussehen, kontrollieren Sie bitte
unter [FAQ](http://faq.oxidmodule.com/Modulinstallation/providerspezifische-Installation/).

##### 4. Modul im Shop aktivieren
Aktivieren Sie das Modul über den Shopadmin */Erweiterungen/Module/*.
Klicken Sie nach Auswahl von *"D³ Heidelpay Payolution Addon"* auf den Button *"Aktivieren"*.  
  
**Wichtig für Enterprise Edition:**  
Achten Sie darauf, dass das Modul in weiteren Shops
(Mall) ebenfalls aktiviert werden muss, um dessen
Funktion dort auch zu nutzen.

##### 5. Shopanpassungen installieren
Direkt nach der Modulaktivierung startet der Installationsassistent, der Sie durch die
Shopanpassung führt. Darin können Sie verschiedene Optionen der Installation
wählen.  
Den Installationsassistenten finden Sie auch unter den Menüpunkten */Admin/D3 Module/Modul-Connector/Modulverwaltung/Modulinstallation/*.  

Bei tiefgreifenden Änderungen an Ihrem Shop (z.B. Hinzufügen weiterer Sprachen
oder Mandanten) rufen Sie den Installationsassistenten bitte erneut auf, um dann
eventuell notwendige Nacharbeiten für das Modul ausführen zu lassen.

Möchten Sie die Änderungen manuell installieren, können Sie sich über diesen
Assistenten ebenfalls eine Checkliste erstellen.

##### 6. TMP-Ordner leeren
Leeren Sie das Verzeichnis "tmp" über */Admin/D3 Module/Modul-Connector/TMP leeren/*.  
Markieren Sie *"komplett leeren"* und klicken auf *"TMP leeren"*.  
Sofern die Views nicht automatisch aktualisiert werden, führen Sie dies noch durch.  
Erfordert Ihre Installation eine andere Vorgehensweise zum Leeren des Caches oder
zum Aktualisieren der Datenbank-Viewtabellen, führen Sie diese bitte aus.

##### 7. Lizenzschlüssel eintragen
Das Modul verwendet Lizenzschlüssel vom Modul D³ Heidelpay.  
Besuchen Sie unseren [Moduleshop](http://www.oxidmodule.com/), um mehr darüber zu erfahren.  
Rufen Sie zum Anfordern des Lizenzschlüssels die Modulverwaltung im Adminbereich
unter */D3 Module/Modul-Connector/Modulverwaltung/* auf.  
Klappen Sie den Eintrag des jeweiligen Moduls aus. Sofern erforderlich, können Sie
hier den Lizenzassistenten starten, der schnell und einfach ihr Modul aktiviert.

##### 8. Konfiguration einstellen
Im Admin wird ein neuer Punkt */D3 Module/Heideplay/Einstellungen/Payolution/* gezeigt, unter
dem alle Einstellungsmöglichkeiten zu finden sind. 

##### 9. Updatefähigkeit
Bei individuellen Änderungen von Moduldateien empfehlen wir, jeweils die
Überladungsmöglichkeiten des Shops dafür zu verwenden. So brauchen Sie die
originalen Moduldateien nicht verändern und erhalten sich so die Updatefähigkeit des
Shops und des Moduls.

Weitere Informationen zu den Überladungsmöglichkeiten verschiedener Dateien
finden Sie in unserer [FAQ](http://faq.oxidmodule.com/Modulinstallation/Modulanpassungen/).

## Update
##### 1. Connector kontrollieren
Kontrollieren Sie bitte die Version unseres Modul-Connectors im Adminbereich unter
*/D3 Module/Modul-Connector/* auf Updates.

##### 2. Modul deaktivieren
Deaktivieren Sie das Modul über den Shopadmin */Erweiterungen/Module/*.  
Klicken Sie nach Auswahl von *"D³ Heidelpay Payolution Addon"* auf den Button *"Deaktivieren"*.

##### 3. Dateien erneuern
Starten Sie die Konsole Ihres Webservers und wechseln in das Hauptverzeichnis Ihres
Shops (oberhalb des source/ - und vendor/ - Verzeichnisses). Senden Sie dort
diesen Befehl ab:  
```composer update d3/heidelpay_payolution –-no-dev```  

Benötigt Ihre Installation einen anderen Aufruf von Composer, ändern Sie den Befehl
bitte entsprechend ab.

Für weitere Optionen dieses Befehls lesen Sie bitte die
[Dokumentation von Composer](https://getcomposer.org/doc/03-cli.md#update).

##### 4. Providerspezifische Installation
Manche Provider erfordern besondere Einstellungen für installierte Module. Ob Ihr
Anbieter spezielle Anforderungen stellt und wie diese aussehen, kontrollieren Sie
bitte unter http://faq.oxidmodule.com/Modulinstallation/providerspezifische-Installation/.

##### 5. Modul aktivieren
Wechseln Sie im Adminbereich zu */Erweiterungen/Module/*. Klicken Sie
nach Auswahl von *"D³ Heidelpay"* auf den Button *"Aktivieren"*.

##### 6. Shopanpassungen installieren
Ob Shopanpassungen notwendig sind, ist von der Versionsänderung des Moduls
abhängig.  
Möglicherweise sehen Sie nach dem Neuaktivieren des Moduls den
Installationsassistent, der Sie durch die Änderungen führt. Folgen Sie dann den
einzelnen Schritten. Möchten Sie die Änderungen manuell installieren, können Sie
sich über diesen Assistenten ebenfalls eine Checkliste erstellen.  
Wird der Assistent nicht gezeigt (Sie sehen wieder die Modulübersicht), waren keine
Anpassungen am Shop notwendig.

Ob erforderliche Updates ausgeführt werden sollen, können Sie jederzeit im
Adminbereich unter */D3 Module/Modul-Connector/Modulverwaltung/Modulinstallation/* prüfen.

##### 7. TMP-Ordner leeren
Leeren Sie das Verzeichnis *"tmp"* über */Admin/D3 Module/Modul-Connector/TMP leeren/*. 
Markieren Sie *"komplett leeren"* und
klicken auf *"TMP leeren"*.  
Sofern die Views nicht automatisch aktualisiert werden, führen Sie dies noch durch.
Erfordert Ihre Installation eine andere Vorgehensweise zum Leeren des Caches oder
zum Aktualisieren der Datenbank-Viewtabellen, führen Sie diese bitte aus


## Deinstallation
##### 1. Modulerweiterungen (sofern vorhanden) deaktivieren und entfernen
Deaktivieren Sie alle vorhandenen Erweiterungen, die auf dem Modul *"D³
Heidelpay Payolution Addon"* aufbauen und löschen bitte alle Dateien dieser Erweiterungen.  
Entfernen Sie ebenfalls alle individuellen Templateanpassungen für dieses Modul.

##### 2. Modul deaktivieren
Deaktivieren Sie das Modul *"D³ Heidelpay Payolution Addon"* über den Shopadmin */Erweiterungen/Module/*.  
Klicken Sie nach Auswahl von *"D³ Heidelpay Payolution Addon"* auf den Button *"Deaktivieren"*.

##### 3. Modul aus der Installation entfernen
Starten Sie die Konsole Ihres Webservers und wechseln in das Hauptverzeichnis Ihres
Shops (oberhalb des source/ - und vendor/ - Verzeichnisses). Senden Sie dort
diesen Befehl ab:  
```composer remove d3/heidelpay_payolution –-no-update```  
Benötigt Ihre Installation einen anderen Aufruf von Composer, ändern Sie den Befehl
bitte entsprechend ab. 

Für weitere Optionen dieses Befehls lesen Sie bitte die
[Dokumentation von Composer](https://getcomposer.org/doc/03-cli.md#remove).

##### 4. Dateien löschen
Löschen Sie den Ordner *"hppayolution/"* und seine enthaltenen Elemente aus dem
Verzeichnis *"source/modules/d3/"* Ihres Shops.

## Hilfe und Support
Bei Bedarf bieten wir Ihnen auch gern die Installation des Moduls in Ihrem Shop an. Geben Sie
uns bitte unter den unten genannten Kontaktdaten Bescheid.  
Haben Sie Fragen oder Unklarheiten in Verbindung mit diesem Modul oder dessen
Installation, stehen Ihnen Hilfetexte in unserer [Modul-FAQ](http://faq.oxidmodule.com/) zur
Verfügung.  
Finden Sie darin die benötigten Antworten nicht, kontaktieren Sie uns bitte unter
den folgenden Möglichkeiten:
- per E-Mail: support@shopmodule.com oder
- über das Kontaktformular auf http://www.oxidmodule.com/ oder
- per Telefon: (+49) 37 21 – 26 80 90 zu unseren Bürozeiten  

Geben Sie bitte an, wo und wie wir gegebenenfalls vorhandene Schwierigkeiten nachvollziehen
können. Sind Ihre Fragen shopspezifisch, benötigen wir möglicherweise Zugangsdaten zum
betreffenden Shop.


Wir wünschen Ihnen mit Ihrem Shop und dem Modul viel Erfolg!  
Ihr D³-Team.
