---Vereisten---
Server:
-PHP versie 7.0 
-Cron
-Mb-strings



---E-mail instellingen---
In 'input/config.php' stel je bij 'SMTP settings' de e-mailinstellingen in.
Je kunt kiezen voor SMTP en Sendmail. SMTP heeft de voorkeur.

SMTP)
1) Vul de gegevens in van "host", "port", "encryption", "username" en "password"
2) Zet "smtp_account" op 'true'.

Sendmail)
1) Zet "smtp_account" op 'false'.




---Bedrijfsgegevens instellen---
Open 'input/config.php' en pas het volgende aan:

1) Bij 'General settings' geef je bij "report mail" aan, naar welk mailadres het report na afloop gestuurd moet worden. (Laat de aanhalingstekens staan!)

2) Bij 'Bunsiness settings' vul je per land waar je aan wilt mailen de gegevens in van je bedrijf (controlleer dit goed!)

Bij "invoiceTranslation" vul je in hoe je het woord 'factuur' schrijft in het betreffende land.
Bij "myEmail" moet je een mailadres invullen dat hoort bij het domein (dit wordt vermeld als afzender).

Laat de aanhalingstekens intact. Onderstaande:
"myCompany" => "Naam",
kun je dus veranderen in:
"myCompany" => "Andere naam",




---Logo's uploaden---
Upload een logo per land naar de map 'input'.
Bestandsnamen: Nederland (logo_nl.png), Belgie (logo_be.png) en Duitsland (logo_de.png).
Resolutie: hoogte minimaal 85 pixels, maximale breedte niet meer dan drie keer de hoogte. Maak het bestand niet te groot.




---Uploaden programma---
Upload alle bestanden naar rootmap van de website (in je browser is dat naamvanjewebsite.tld/, maar op de server heet de map waar het in moet iets als 'public_html').




---Instellen Cron en PHP---
1) Log in bij je host en stel in dat er gebruikt moet worden gemaakt van PHP 7.0
2) Log in bij de host en maak een cronjob aan die elke vijf minuten het bestand cron.php aanspreekt.




---Voorbereiden en starten maillijst---
1) In 'input/config.php' stel je bij 'General settings' het land in van de bedrijven die je gaat mailen (één land per keer!) en het mailadres waarnaar na afloop het rapport moet worden gemaild.

Nederland:
"country" => "nl",
Belgie:
"country" => "be",
Duitsland:
"country" => "de",

2) Controlleer of alles verder goed is ingesteld (zie hierboven).

3) Controlleer of 'list.csv' voldoet aan de eisen. De headers moeten blijven zoals in het voorbeeldbestand. Zorg er ook voor dat je de factuurnummers hebt ingevoerd.

4) Upload 'list.csv' naar de map 'input'.

5) Ga naar 'input/config.php' en zet bij 'General setting' "enabled" op 'true'.

6) Het programma begint te lopen en je ontvangt een e-mail ter bevestiging.







