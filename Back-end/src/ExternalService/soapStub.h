/* soapStub.h
   Generated by gSOAP 2.8.16 from rcx.h

Copyright(C) 2000-2013, Robert van Engelen, Genivia Inc. All Rights Reserved.
The generated code is released under one of the following licenses:
GPL or Genivia's license for commercial use.
This program is released under the GPL with the additional exemption that
compiling, linking, and/or using OpenSSL is allowed.
*/

#ifndef soapStub_H
#define soapStub_H
#define SOAP_NAMESPACE_OF_ns2	"http://tempuri.org/Imports"
#define SOAP_NAMESPACE_OF_ns1	"http://tempuri.org/"
#define SOAP_NAMESPACE_OF_ns3	"http://schemas.microsoft.com/2003/10/Serialization/"
#include "stdsoap2.h"
#if GSOAP_VERSION != 20816
# error "GSOAP VERSION MISMATCH IN GENERATED CODE: PLEASE REINSTALL PACKAGE"
#endif

#ifdef __cplusplus
extern "C" {
#endif

/******************************************************************************\
 *                                                                            *
 * Enumerations                                                               *
 *                                                                            *
\******************************************************************************/


#ifndef SOAP_TYPE_xsd__boolean
#define SOAP_TYPE_xsd__boolean (11)
/* xsd:boolean */
enum xsd__boolean { xsd__boolean__false_ = 0, xsd__boolean__true_ = 1 };
#endif

/******************************************************************************\
 *                                                                            *
 * Types with Custom Serializers                                              *
 *                                                                            *
\******************************************************************************/


/******************************************************************************\
 *                                                                            *
 * Classes and Structs                                                        *
 *                                                                            *
\******************************************************************************/


#if 0 /* volatile type: do not declare here, declared elsewhere */

#endif

#ifndef SOAP_TYPE_xsd__base64Binary
#define SOAP_TYPE_xsd__base64Binary (7)
/* Base64 schema type: */
struct xsd__base64Binary
{
	unsigned char *__ptr;
	int __size;
	char *id;	/* optional element of type xsd:string */
	char *type;	/* optional element of type xsd:string */
	char *options;	/* optional element of type xsd:string */
};
#endif

#ifndef SOAP_TYPE__ns1__GetListINC
#define SOAP_TYPE__ns1__GetListINC (22)
/* ns1:GetListINC */
struct _ns1__GetListINC
{
	char *sigkey;	/* optional element of type xsd:string */
};
#endif

#ifndef SOAP_TYPE__ns1__GetListINCResponse
#define SOAP_TYPE__ns1__GetListINCResponse (23)
/* ns1:GetListINCResponse */
struct _ns1__GetListINCResponse
{
	char *GetListINCResult;	/* SOAP 1.2 RPC return element (when namespace qualified) */	/* optional element of type xsd:string */
};
#endif

#ifndef SOAP_TYPE__ns1__UpdateStatusINC
#define SOAP_TYPE__ns1__UpdateStatusINC (24)
/* ns1:UpdateStatusINC */
struct _ns1__UpdateStatusINC
{
	char *INCCode;	/* optional element of type xsd:string */
	char *INCStatusID;	/* optional element of type xsd:string */
	char *ITSMCode;	/* optional element of type xsd:string */
	char *CreatedBy;	/* optional element of type xsd:string */
	char *Comment;	/* optional element of type xsd:string */
	char *ITSMCloseDate;	/* optional element of type xsd:string */
	char *sigkey;	/* optional element of type xsd:string */
};
#endif

#ifndef SOAP_TYPE__ns1__UpdateStatusINCResponse
#define SOAP_TYPE__ns1__UpdateStatusINCResponse (25)
/* ns1:UpdateStatusINCResponse */
struct _ns1__UpdateStatusINCResponse
{
	char *UpdateStatusINCResult;	/* SOAP 1.2 RPC return element (when namespace qualified) */	/* optional element of type xsd:string */
};
#endif

#ifndef SOAP_TYPE___ns1__GetListINC
#define SOAP_TYPE___ns1__GetListINC (29)
/* Operation wrapper: */
struct __ns1__GetListINC
{
	struct _ns1__GetListINC *ns1__GetListINC;	/* optional element of type ns1:GetListINC */
};
#endif

#ifndef SOAP_TYPE___ns1__UpdateStatusINC
#define SOAP_TYPE___ns1__UpdateStatusINC (33)
/* Operation wrapper: */
struct __ns1__UpdateStatusINC
{
	struct _ns1__UpdateStatusINC *ns1__UpdateStatusINC;	/* optional element of type ns1:UpdateStatusINC */
};
#endif

#ifndef WITH_NOGLOBAL

#ifndef SOAP_TYPE_SOAP_ENV__Header
#define SOAP_TYPE_SOAP_ENV__Header (34)
/* SOAP Header: */
struct SOAP_ENV__Header
{
#ifdef WITH_NOEMPTYSTRUCT
	char dummy;	/* dummy member to enable compilation */
#endif
};
#endif

#endif

#ifndef WITH_NOGLOBAL

#ifndef SOAP_TYPE_SOAP_ENV__Code
#define SOAP_TYPE_SOAP_ENV__Code (35)
/* SOAP Fault Code: */
struct SOAP_ENV__Code
{
	char *SOAP_ENV__Value;	/* optional element of type xsd:QName */
	struct SOAP_ENV__Code *SOAP_ENV__Subcode;	/* optional element of type SOAP-ENV:Code */
};
#endif

#endif

#ifndef WITH_NOGLOBAL

#ifndef SOAP_TYPE_SOAP_ENV__Detail
#define SOAP_TYPE_SOAP_ENV__Detail (37)
/* SOAP-ENV:Detail */
struct SOAP_ENV__Detail
{
	char *__any;
	int __type;	/* any type of element <fault> (defined below) */
	void *fault;	/* transient */
};
#endif

#endif

#ifndef WITH_NOGLOBAL

#ifndef SOAP_TYPE_SOAP_ENV__Reason
#define SOAP_TYPE_SOAP_ENV__Reason (40)
/* SOAP-ENV:Reason */
struct SOAP_ENV__Reason
{
	char *SOAP_ENV__Text;	/* optional element of type xsd:string */
};
#endif

#endif

#ifndef WITH_NOGLOBAL

#ifndef SOAP_TYPE_SOAP_ENV__Fault
#define SOAP_TYPE_SOAP_ENV__Fault (41)
/* SOAP Fault: */
struct SOAP_ENV__Fault
{
	char *faultcode;	/* optional element of type xsd:QName */
	char *faultstring;	/* optional element of type xsd:string */
	char *faultactor;	/* optional element of type xsd:string */
	struct SOAP_ENV__Detail *detail;	/* optional element of type SOAP-ENV:Detail */
	struct SOAP_ENV__Code *SOAP_ENV__Code;	/* optional element of type SOAP-ENV:Code */
	struct SOAP_ENV__Reason *SOAP_ENV__Reason;	/* optional element of type SOAP-ENV:Reason */
	char *SOAP_ENV__Node;	/* optional element of type xsd:string */
	char *SOAP_ENV__Role;	/* optional element of type xsd:string */
	struct SOAP_ENV__Detail *SOAP_ENV__Detail;	/* optional element of type SOAP-ENV:Detail */
};
#endif

#endif

/******************************************************************************\
 *                                                                            *
 * Typedefs                                                                   *
 *                                                                            *
\******************************************************************************/

#ifndef SOAP_TYPE__QName
#define SOAP_TYPE__QName (5)
typedef char *_QName;
#endif

#ifndef SOAP_TYPE__XML
#define SOAP_TYPE__XML (6)
typedef char *_XML;
#endif

#ifndef SOAP_TYPE_xsd__byte
#define SOAP_TYPE_xsd__byte (12)
typedef char xsd__byte;
#endif

#ifndef SOAP_TYPE_xsd__unsignedByte
#define SOAP_TYPE_xsd__unsignedByte (13)
typedef unsigned char xsd__unsignedByte;
#endif

#ifndef SOAP_TYPE_xsd__ID
#define SOAP_TYPE_xsd__ID (14)
typedef char *xsd__ID;
#endif

#ifndef SOAP_TYPE_xsd__IDREF
#define SOAP_TYPE_xsd__IDREF (15)
typedef char *xsd__IDREF;
#endif

#ifndef SOAP_TYPE_xsd__anyURI
#define SOAP_TYPE_xsd__anyURI (16)
typedef char *xsd__anyURI;
#endif

#ifndef SOAP_TYPE_xsd__decimal
#define SOAP_TYPE_xsd__decimal (17)
typedef char *xsd__decimal;
#endif

#ifndef SOAP_TYPE_xsd__duration
#define SOAP_TYPE_xsd__duration (18)
typedef char *xsd__duration;
#endif

#ifndef SOAP_TYPE_ns3__char
#define SOAP_TYPE_ns3__char (19)
typedef int ns3__char;
#endif

#ifndef SOAP_TYPE_ns3__duration
#define SOAP_TYPE_ns3__duration (20)
typedef char *ns3__duration;
#endif

#ifndef SOAP_TYPE_ns3__guid
#define SOAP_TYPE_ns3__guid (21)
typedef char *ns3__guid;
#endif


/******************************************************************************\
 *                                                                            *
 * Externals                                                                  *
 *                                                                            *
\******************************************************************************/


/******************************************************************************\
 *                                                                            *
 * Server-Side Operations                                                     *
 *                                                                            *
\******************************************************************************/


SOAP_FMAC5 int SOAP_FMAC6 __ns1__GetListINC(struct soap*, struct _ns1__GetListINC *ns1__GetListINC, struct _ns1__GetListINCResponse *ns1__GetListINCResponse);

SOAP_FMAC5 int SOAP_FMAC6 __ns1__UpdateStatusINC(struct soap*, struct _ns1__UpdateStatusINC *ns1__UpdateStatusINC, struct _ns1__UpdateStatusINCResponse *ns1__UpdateStatusINCResponse);

/******************************************************************************\
 *                                                                            *
 * Server-Side Skeletons to Invoke Service Operations                         *
 *                                                                            *
\******************************************************************************/

SOAP_FMAC5 int SOAP_FMAC6 soap_serve(struct soap*);

SOAP_FMAC5 int SOAP_FMAC6 soap_serve_request(struct soap*);

SOAP_FMAC5 int SOAP_FMAC6 soap_serve___ns1__GetListINC(struct soap*);

SOAP_FMAC5 int SOAP_FMAC6 soap_serve___ns1__UpdateStatusINC(struct soap*);

/******************************************************************************\
 *                                                                            *
 * Client-Side Call Stubs                                                     *
 *                                                                            *
\******************************************************************************/


SOAP_FMAC5 int SOAP_FMAC6 soap_call___ns1__GetListINC(struct soap *soap, const char *soap_endpoint, const char *soap_action, struct _ns1__GetListINC *ns1__GetListINC, struct _ns1__GetListINCResponse *ns1__GetListINCResponse);

SOAP_FMAC5 int SOAP_FMAC6 soap_call___ns1__UpdateStatusINC(struct soap *soap, const char *soap_endpoint, const char *soap_action, struct _ns1__UpdateStatusINC *ns1__UpdateStatusINC, struct _ns1__UpdateStatusINCResponse *ns1__UpdateStatusINCResponse);

#ifdef __cplusplus
}
#endif

#endif

/* End of soapStub.h */
