# Digital Signature Sighter Signing module
## Scribed for XOOPS 2.5 ~ http://xoops.org - PHP Framework Portal
## Author: Simon Antony Roberts (wishcraft)
### Contact: simon@xcp.solutions (Sydney)
### Skype: antony.cipher
### Demo: http://signed.snails.email

The Digital Signature Sighter Signing module for XOOPS 2.5 is a module that allows your users of 
your website to have a self signed digital certificate store on your site; the module also has an
API that allows any other website via a self discovery API in the headers of the module to dial a
site and get with their specified document identication and details with search results for
authentication with their signature through calling the api from a parrallel site, you can see this
in API Calling routines help on the api in the demo.

So when a client of you website generates a signature their is a set of fields in the XOOPS_LIB folder
that defines all the fields and tables of information for a signature; this is currently set for 100
points ID Signatures with the ID Uploaded and stored - now the interesting thing veristy parity of signed.

Which once you have establised a xoops_data/signed you will not be able to move it with out starting the
Signatures from start, these are also encrypted if you turn it on with the new cryptus class with the
module. 

Signed 2.21 is the final realease of the digital signature framework for XOOPS, it allows your clients
to generate online signatures that can be validated by the signer from your signee vaults with correct
identification etc. It also unsigns all signatures if for example a presented ID Card with water mark 
when it is checked on a signature is invalid or expired as well when signatures themselves are cancelled;
you can modify it to include the standard fields as well as fields in other certificates that will allow
for you in a pretty similar manner with the XCP Checksume (eXtensible Checksum Process) to generate other
certificate formats and lengths with easy to modify routines that will in most conditions when the ID
in it out ranks or is correct rank will divinate other certificate types and locks.

# Installation

Once you have your copy of XOOPS installed copy the folder corresponding in the archive to the correct
locations and then log into XOOPS go to the module admin and install the module, it is that simple!

# Bug Reports

To report bugs please us http://sourceforge.net/p/chronolabs/tickets remember to report your OS, XOOPS Version
MySQL Version and PHP Version for the module.
