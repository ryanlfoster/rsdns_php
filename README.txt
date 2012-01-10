Documentation for rsdns_php

rsdns class:

this class Abstracts the dns api away from curl.

It implements a class constructor which takes in a username/password/
"boolean value to the question "is this A UK account?"

Example:
$some_var = new rsdns('USERNAME','API_KEY',(TRUE|FALSE));

Client Accounts:

  The rsdns class will initialize to your main account ID automatically.
  Right now you have to initialize the object first THEN switch to the account
  number you want to use before you run your next operation. (Your account must 
  have control over the client account in question additionally as of this 
  writing these sub-accounts can only be created in the cloud sites control 
  panel). 
  e.g. 

  $comm = new rsdns('USERNAME','API_KEY',(FALSE));

  //save a copy of your account number
  $my_account = $comm->account;

  //change the account we are pointed at
  $comm->account = 'NEXT_ACCOUNT_ID';

  //now we can print the domains in "NEXT_ACCOUNT_ID".
  print $comm->domain_list(); 

  //now if you want to print domains from your account you have to change it
  //back to what it was. 
  $comm->account = $my_account;

  //now we are printing from the main account again
  print $comm->domain_list(); 
  
Notes:

  


The rest of this document conforms to the "4. API Operations" 
Section of the DNS API Developers Guide. Located here:
http://docs.rackspace.com/cdns/api/v1.0/cdns-devguide/content/index.html

So if you can find the operation in there you should be able to find the 
operation here. And under the same bullet point.

EXAMPLES and documentation:

4.1. Limits:

  4.1.1. List All Limits
      This call provides a list of all applicable limits for the specified 
      account.
      synchronous calls:
	rsdns :: limit_all();

  4.1.2. List Limit Types
      This call provides a list of all applicable limit types for the specified 
      account.
      synchronous calls:
	rsdns :: limit_types();

  4.1.3. List Specific Limits
      This call provides a list of all applicable limits of the specified type
      for the specified account.
      synchronous calls:
	rsdns :: limit_check('LIMIT_TYPE');

4.2. Domains

  4.2.1. List Domains
      These calls provide a list of all DNS domains manageable by a given 
      account. The resulting list is flat, and does not break the domains down 
      hierarchically by sub-domain. All representative domains are included in 
      the list, even if a domain is conceptually a sub-domain of another domain 
      in the list.
      synchronous calls:
	rsdns :: domain_list();

      4.2.1.1. Search Domains with Filtering
	As illustrated by the examples above, the List Domains call provides a 
	list of all DNS domains manageable by a given account. Filtering the 
	search to limit the results returned can be performed by using the name 
	parameter on the List Domains call. For example, ?﻿name=hoola.com matches
	hoola.com and similar names such as main.hoola.com and sub.hoola.com.
	synchronous calls:
	  rsdns :: domain_search('SEARCH_TERM');

  4.2.2. List Domain Details
      This call provides the detailed output for a specific domain configured 
      and associated with an account. This call is not capable of returning 
      details for a domain that has been deleted.
      This function takes in a value which returns all records of this domain. 
      And a value which returns sub-domain details for this domain.
      synchronous calls:
	rsdns :: domain_details('DOMAIN_ID','Boolean records',
				 'Boolean sub-domains');

  4.2.3. List Domain Changes
      This call shows all changes to the specified domain since the specified 
      date/time. The since parameter is optional and defaults to midnight of the
      current day. See Section 3.9, “Date/Time Format” for details on how to 
      specify this parameter's value.
      synchronous calls:
	rsdns :: domain_changes('DOMAIN_ID','TIME_STRING')

  4.2.4. Export Domain
	  This call provides the BIND (Berkeley Internet Name Domain) 9 formatted 
	  contents of the requested domain. This call is for a single domain only, 
	  and as such, does not traverse up or down the domain hierarchy for details
	  (that is, no sub-domain information is provided). This function uses my 
	  internal status(Appendix 1) function. Which can be passed it's own options. 
	  
	  Asynchronous call:
	rsdns :: domain_export('DOMAIN_ID','boolean keep-alive')

  4.2.5. Create Domain(s)
	  This call provisions one or more new DNS domains under the account 
	  specified, based on the configuration defined in the request object. If 
	  the corresponding request cannot be fulfilled due to insufficient or 
	  invalid data, an HTTP 400 (Bad Request) error response will be returned 
	  with information regarding the nature of the failure in the body of the 
	  response. Failures in the validation process are non-recoverable and 
	  require the caller to correct the cause of the failure and POST the 
	  request again.
	  
	  Asynchronous call:
	rsdns :: domain_create('json_request','boolean keep-alive')

  4.2.6. Import Domain
	  This call provisions a new DNS domain under the account specified by the 
	  BIND 9 formatted file configuration contents defined in the request 
	  object.  If the corresponding request cannot be fulfilled due to 
	  insufficient or invalid data, an HTTP 400 (Bad Request) error response 
	  will be returned with information regarding the nature of the failure in 
	  the body of the response. Failures in the validation process are 
	  non-recoverable and require the caller to correct the cause of the failure
	  and POST the request again.
	  
	  Asynchronous call:
	rsdns :: domain_import('Bind 9 txt', 'boolean keep-alive')

  4.2.7. Modify Domain(s)
	  This call modifies DNS domain(s) attributes only. Records cannot be added,
	  modified, or removed. Only the TTL, email address and comment attributes 
	  of a domain can be modified.
	  If a request cannot be fulfilled due to insufficient or invalid data, an 
	  HTTP 400 (Bad Request) error response will be returned with information 
	  regarding the nature of the failure in the body of the response. Failures 
	  in the validation process are non-recoverable and require the caller to 
	  correct the cause of the failure and POST the request again.
    
	  Asynchronous call:
	rsdns :: domain_modify('json_request','boolean keep-alive')
	rsdns :: domain_modify_any('json_request','boolean keep-alive')
	  
  4.2.8. Remove Domain(s)
	  This call removes one or more specified domains from the account; when a 
	  domain is deleted, its immediate resource records are also deleted from 
	  the account. By default, if a deleted domain had sub-domains, each 
	  sub-domain becomes a root domain and is not deleted; this can be overridden
	  by the optional delete Sub-domains parameter. Utilizing the optional 
	  delete Sub-domains parameter on domains without sub-domains does not result 
	  in a failure. When a domain is deleted, any and all domain data is 
	  immediately purged and is not recoverable via the API. So on a successful 
	  delete, subsequent requests for the deleted object should return 
	  itemNotFound (404).
	  
	  Asynchronous call:
	rsdns :: domain_delete('array of domain IDs','boolean delete Sub-domains','Boolean keep-alive') 
	  

4.3. Subdomains

  4.3.1. List Subdomains
      This call provides a list of all DNS domains that are subdomains of the
      specified domain. The resulting list is flat, and does not break the 
      domains down hierarchically by subdomain.
      synchronous calls:
	rsdns :: subdomain_list('DOMAIN_ID');

4.4. Records

  4.4.1. List Records
      This call lists all records configured for the specified domain.
      synchronous calls:
	rsdns :: record_list('DOMAIN_ID');

  4.4.2. List Record Details
      This call lists details for the specified record in the specified domain.
      synchronous calls:
	rsdns :: record_list_id('DOMAIN_ID','Record_ID')

  4.4.3. Add Records
      This call adds new record(s) to the specified domain.
      Asynchronous calls:
	rsdns :: record_add('DOMAIN_ID','json_request','boolean keep-alive')

      4.4.3.1. Add Wildcard Records
	Users can add one or more wildcard records to any domain or sub-domain 
	on their account. For information on the intent and use of wildcard 
	records, see the DNS literature including RFC 1034, section 4.3.3, and 
	RFC 4595.
    
	4.4.3.1.1 Wildcard Record Resolution
	  The Cloud DNS infrastructure will only match a wildcard if a label at 
	  any level does not exist. 

  4.4.4. Modify Records
      These calls modify the configuration of a specified record or multiple 
      records in the specified domain.
      Asynchronous calls:
	rsdns :: record_modify('DOMAIN_ID','RECORD_ID','json_request','boolean keep-alive')
	rsdns :: record_modify_any('DOMAIN_ID','json_request','boolean keep-alive')

  4.4.5. Remove Records
      These calls remove a specified record or multiple records from the 
      specified domain. When a record is deleted, any and all record data is 
      immediately purged and is not recoverable via the API. So on a successful 
      delete, subsequent requests for the deleted record should return 
      itemNotFound (404).
      Asynchronous calls:
	rsdns :: record_remove('DOMAIN_ID','RECORD_ID','json_request','Boolean keep-alive')
	rsdns :: record_remove_any('DOMAIN_ID','json_request','Boolean keep-alive')

Additional information:


This class also implements a private status function which is responsible for 
Asynchronous responses from the server. It only returns the completed response
and properly waits for it's completion. (Updating every 2 seconds).
The function exits on any one of these conditions. 
1. that the task is completed, 
2. The task is in error state, 
3. It times out at 20 server side seconds. 
Some asynchronous calls can be passed these options where documented.

keepAlive – if true, tries to flush "." to the client every 2 seconds. 

showErrors – if true, specifies that errors are shown

showDetails– if true, specifies that job details are shown

Defaults,
$keepAlive=0,
$ShowErrors=1,
$ShowDetails=1