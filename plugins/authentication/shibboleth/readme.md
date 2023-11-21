## 1. Introduction

This is a modified version of the HUBzero documentation for the [Shibboleth plugin for InCommon](https://help.hubzero.org/documentation/220/installation/debian/addons/incommon), to allow it to be used with Canadian Access Federation (CAF).

This plugin provides the code necessary to allow your hub to accept credentials using the Shibboleth system with CAF.

You will need to get your hub added to the CAF XML manifest as a service provider.


## 2. Installation

Debian

`apt-get install -y libapache2-mod-shib2`

Redhat Enterprise Linux & other distributions

See [Shibboleth wiki entry on service provider installation](https://wiki.shibboleth.net/confluence/display/SHIB2/NativeSPLinuxRPMInstall) for information on how to add the Shibboleth software to your list of repositories so that it can be installed and upgraded through yum, or, failing that, how to install from SRPMS.


## 3. Configuration

### 3.1 Shibboleth

#### 3.1.1 Certificates

As root, run the script `shib-keygen`, which was installed as part of the package. This will generate a key pair for your service provider to use. No further configuration is required for this; the software will find the keys when the shibd service is restarted.

Output:

	Generating a 2048 bit RSA private key
	........................................................................................+++
	.....................+++
	writing new private key to '/etc/shibboleth/sp-key.pem'
	-----

#### 3.1.2 /etc/shibboleth/attribute-map.xml

This file controls which attributes (bits of user information) the software will extract during login when the identity provider makes them available.

Make sure the following pertinent attributes are not commented out in both forms of the “name” attribute.

eppn (username, probably already enabled in the shipped configuration):

    <Attribute name="urn:mace:dir:attribute-def:eduPersonPrincipalName" id="eppn">
        <AttributeDecoder xsi:type="ScopedAttributeDecoder"/>
    </Attribute>
    <Attribute name="urn:oid:1.3.6.1.4.1.5923.1.1.1.6" id="eppn">
        <AttributeDecoder xsi:type="ScopedAttributeDecoder"/>
    </Attribute>

Name & email (probably not enabled by default):

    <Attribute name="urn:mace:dir:attribute-def:sn" id="sn"/>
    <Attribute name="urn:mace:dir:attribute-def:givenName" id="givenName"/>
    <Attribute name="urn:mace:dir:attribute-def:displayName" id="displayName"/>
    <Attribute name="urn:mace:dir:attribute-def:mail" id="mail"/>
    <Attribute name="urn:oid:2.5.4.4" id="sn"/>
    <Attribute name="urn:oid:2.5.4.42" id="givenName"/>
    <Attribute name="urn:oid:2.16.840.1.113730.3.1.241" id="displayName"/>
    <Attribute name="urn:oid:0.9.2342.19200300.100.1.3" id="mail"/>

#### 3.1.3 /etc/shibboleth/shibboleth2.xml

This is the main configuration, which controls how the software federates with identity providers.

First, replace $YOUR_HOSTNAME with your hostname in the entityID attribute near the top of the file:

       <ApplicationDefaults entityID="https://$YOUR_HOSTNAME/login/shibboleth" REMOTE_USER="eppn persistent-id targeted-id">

In the block, delete or comment out any SSO or SessionInitiator blocks that shipped, and add the two listed below if this is a production machine. This tells the software to redirect for a given authentication request to the CAF Central Discovery Service.

        <Sessions lifetime="28800" timeout="3600" relayState="ss:mem"
                  checkAddress="false" handlerSSL="true" cookieProps="https">
            <SSO discoveryProtocol="SAMLDS" ECP="true" discoveryURL="https://caf-shib2ops.ca/DS/CAF.ds">
              SAML2 SAML1
            </SSO>
            <SessionInitiator type="Chaining" Location="/login/shibboleth" isDefault="true" id="Login">
                <SessionInitiator type="SAML2" template="bindingTemplate.html"/>
                <SessionInitiator type="Shib1"/>
                <SessionInitiator type="SAMLDS" URL="https://caf-shib2ops.ca/DS/CAF.ds"/>
            </SessionInitiator>
            <!-- Default <Handler> tags not pictured, but they should stay -->
       </Sessions>

If this is a test/development machine, please use the CAF test site for the Central Discovery Service by replacing the above URL and discoveryURL with "https://ds.caftest.canarie.ca/DS/CAF.ds".

If this is a production machine you will want to set a real email for the support contact:

       <Errors supportContact="support@$YOUR_HOSTNAME"
            helpLocation="/about.html"
            styleSheet="/shibboleth-sp/main.css"/>

Finally, you will need to configure how and where the software looks for metadata about identity providers. This is just a list of providers you can support, including some helpful annotations like where the service URLs and what public key to use when communicating with it.

If your plans include membership in CAF, please add the following lines below the Sessions tag and at the same scope:

	<MetadataProvider type="XML" uri="http://caf-shib2ops.ca/CoreServices/caf_metadata_signed_sha256.xml" backingFilePath="CAF-metadata.xml" reloadInterval="3600">
            <MetadataFilter type="Signature" certificate="caf_metadata_verify.crt"/>
	</MetadataProvider>

Install https://caf-shib2ops.ca/CoreServices/caf_metadata_verify.crt as /etc/shibboleth/caf_metadata_verify.crt so it’s available for this provider.

If this is a test/development manchine, please use the Test Federation by replacing the URI in the MetadataProvider with "https://caf-shib2ops.ca/CoreServices/testbed/caf_test_fed_unsigned.xml".


### 3.2 Apache

#### 3.2.1 /etc/apache2/sites-enabled/{your-ssl-enabled-config-file}

Your EntityID is something like https://hostname/login/shibboleth, but the actual URL to pick up the login process again in HUBzero CMS terms is more complicated, so we rewrite it. I recommend putting this statement as high as possible in the config (after RewriteEngine on) so that the “L“ast last triggers and you can be assured the URL is not subsequently rewritten by anything else you’re doing.

        RewriteCond     %{REQUEST_URI}          ^/login/shibboleth
        RewriteRule     (.*) /index.php?option=com_users&authenticator=shibboleth&task=user.login [NC,L]

Bind an endpoint to the module. This is used during the login process and is also useful to get a basis for your service provider’s metadata, which is served at /Shibboleth.sso/Metadata when the request comes from localhost.

        <Location /Shibboleth.sso>
                SetHandler shib
        </Location>

You probably have a rule that directs all requests that appear to be for HUBzero CMS content to the index.php bootstrap, and we need to note that /Shibboleth.sso isn’t HUBzero CMS business, so make sure you have a RewriteCond like this:

        RewriteCond     %{REQUEST_URI}          !^/Shibboleth.sso/.*$ [NC]
        RewriteRule     (.*)                    index.php

Finally, we actually protect the entityID location /login/shibboleth. We can redirect a user to this path to require them to make a Shibboleth login. Shibboleth won’t know specifically how to do that so it will make a request to the wayf location defined above in shibboleth2.xml. This is part of the HUBzero CMS that knows already which provider the user selected from the login page, so it spits back the appropriate identity provider entityId. From there the metadata is referenced to find the endpoint associated with that institution, and the user is sent to the login page. They come back to /login/shibboleth upon submission, but now the requirement to have a Shibboleth session is satisfied, and the rewritten URL referencing user.login is served to complete the process.

        <Location /login/shibboleth>
                AuthType shibboleth
                ShibRequestSetting requireSession 1
                Require valid-user
        </Location>

Restart the shibd and apache2 services when satisfied with this configuration.


## 4. HUBzero CMS Plugin

Log in to /administrator, choose "Extensions" and then "Plug-in Manager", and locate the Shibboleth plugin in the Authentication category and then enable it.
