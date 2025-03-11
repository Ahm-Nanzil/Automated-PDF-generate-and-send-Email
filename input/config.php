<?php $conf = [
####################
# General settings #
####################

# enabled: The main switch. set to 'true' to enable the SSMachine or 'false' to turn it off.
"enabled"       => false,
# country: This decides which templates are used. Current options are: 'be', 'nl' and 'de'.
"country"       => "",
# report_mail: Send start notification & final report to this e-mail address
"report_mail"   => "admin@",
# max_per_run: The amount of e-mail to process per run.
# IMPORTANT! The max value is 20! This is due to the max php script execution time and to prevent e-mail server triggers.
# You might have to reduce this value on too many failures.
"max_per_run"   => "1",
# timezone: The timezone you want to use for setting dates/time. See: https://www.php.net/manual/en/timezones.php for the options.
"timezone"      => "",

#################
# SMTP settings #
#################

# smtp_account: Options are 'true' or 'false'.
# When you need to authenticate to be able to e-mail from this server, set this to 'true' and specify the SMTP settings below.
# If this is set to false it uses the sendmail functionality, which might work on Linux servers (only).
# IMPORTANT! Make sure that the SMTP server is allowed to e-mail from the domain name you're going to use as the 'from' address.
# So create and use an e-mail address at the server where your domain name is registered or e-mail sending won't work!
"smtp_account"   => false,
# smtp_settings: If you've set the above to 'true', you MUST specify valid e-mail account settings below.
"smtp_settings"  => [
    # host: The SMTP host/servername.
    "host"       => "mail.example.tld",
    # port: The SMTP server port.
    "port"       => "465",
    # encryption: The encryption to use for authentication. Can be empty '', 'tls' or 'ssl'.
    "encryption" => "SSL",
    # username: Your SMTP username (most of the time this is the same as your e-mail address).
    "username"   => "info@example.tld",
    # password: Your SMTP password.
    "password"   => "mys3cr3t",
],

#####################
# Business settings #
#####################

# business: Within the business array you can define additional countries.
# If you add a new country, you then also have to add new templates and a logo.
# Based on the 'country' choosen above (General settings), the data from below is used for the invoice and e-mail generation.
"business" => [
    "nl" => [
        "myCompany"         => "Company name",
        "myStreet"          => "Street 123",
        "myZip"             => "1234AB",
        "myCity"            => "Abcoude",
        "myCountry"         => "Nederland",
        "myPhone"           => "+31 85 3033366",
        "myWebsite"         => "www.test.com",
        "myEmail"           => "info@test.com",
        "myVat"             => "NL0019999536",
        "myCoc"             => "12345678",
        "myIban"            => "NL77 ABCD 0001 2345 67",
        "myBic"             => "-",
        "myBankAccountName" => "Company name",
        "invoiceTranslation"=> "factuur",
        "invoiceBcc"        => "",
    ],
    "be" => [
        "myCompany"         => "Company name",
        "myStreet"          => "Priester Cuypersstraat 6",
        "myZip"             => "1040",
        "myCity"            => "Bruxelles",
        "myCountry"         => "België",
        "myPhone"           => "+31 85 3033366",
        "myWebsite"         => "www.test.com",
        "myEmail"           => "info@test.com",
        "myVat"             => "NL012345678",
        "myCoc"             => "12345678",
        "myIban"            => "NL77 ABCD 0001 2345 67",
        "myBic"             => "-",
        "myBankAccountName" => "Company Name",
        "invoiceTranslation"=> "facture",
        "invoiceBcc"        => "",
    ],
    "de" => [
        "myCompany"         => "Company name",
        "myStreet"          => "Prager Str. 191,",
        "myZip"             => "04317",
        "myCity"            => "Leipzig",
        "myCountry"         => "Deutschland",
        "myPhone"           => "+31 85 3033366",
        "myWebsite"         => "www.test.com",
        "myEmail"           => "info@test.com",
        "myVat"             => "NL123456788",
        "myCoc"             => "12345678",
        "myIban"            => "NL77 ABCD 0001 2345 67",
        "myBic"             => "-",
        "myBankAccountName" => "Comapny Name",
        "invoiceTranslation"=> "rechnung",
        "invoiceBcc"        => "",
    ],
],

#######################
# Required CSV fields #
#######################

# required_fields: If the below fields are NOT present as header / value in the input file the line will be logged and skipped.
"required_fields" => ['company','street','zip','city','email1','website1','invoiceNumber'],

#############
# Debugging #
#############

# debug: Set to 'true', to enable debugging. Alle e-mail will then be send from/to the values as set below.
"debug"             => false,
# debug_mail_to: E-mail address to send invoices & reports to
"debug_mail_to"     => "test@@test.com",
# debug_mail_from: E-mail address to use as sender addres while debugging
"debug_mail_from"   => "test@@test.com",

];?>
