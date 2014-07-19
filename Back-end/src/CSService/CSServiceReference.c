#include "stdio.h"
#include <iostream>
#include <string.h>
#include <unistd.h>		/* defines _POSIX_THREADS if pthreads are available */
#if defined(_POSIX_THREADS) || defined(_SC_THREADS)
#include <pthread.h>
#endif
#include <signal.h>		/* defines SIGPIPE */
#include "soapH.h"
#include "BasicHttpBinding_USCOREISDKSuportSerivces.nsmap"
#include "CSServiceReference.h"
using namespace std;

string CallUpdateStatusCSINC(char *pINCCode, short *pINCStatusID, char *pITSMCode, char *pCreatedBy, char *pComment, char *pITSMCloseDate)
{
	string strResult;
	struct soap soap;
	struct _ns1__UpdateStatusINC tagUpdateStatusINCRequest;
	struct _ns1__UpdateStatusINCResponse tagUpdateStatusINCResponse;

	//Assign parameter to UpdateStatusINC Request
	tagUpdateStatusINCRequest.INCCode = pINCCode;
	tagUpdateStatusINCRequest.INCStatusID = pINCStatusID;
	tagUpdateStatusINCRequest.ITSMCode = pITSMCode;
	tagUpdateStatusINCRequest.CreatedBy = pCreatedBy;
	tagUpdateStatusINCRequest.Comment = pComment;
	tagUpdateStatusINCRequest.ITSMCloseDate = pITSMCloseDate;
	tagUpdateStatusINCRequest.sigkey = (char*)SECKEY;
	cout << "SECKEY:" << tagUpdateStatusINCRequest.sigkey << endl;
	
	//****************************
	//soap_init
	//****************************
	try
	{
		soap_init(&soap);
	}
	catch(exception& ex)
	{
		
		return CODE_ERROR_INIT;
	}
	//****************************
	soap.connect_timeout = 60;	/* try to connect for 1 minute */
	soap.send_timeout = soap.recv_timeout = 90;	/* if I/O stalls, then timeout after 30 seconds */
	//****************************
	
	
	if(soap_call___ns1__UpdateStatusINC(&soap, 
								NULL  /*endpoint address*/, 
								NULL  /*soapAction*/, 
								&tagUpdateStatusINCRequest,
								&tagUpdateStatusINCResponse
							   )== SOAP_OK)
	{
		strResult = tagUpdateStatusINCResponse.UpdateStatusINCResult;
	}
	else
	{   
		return CODE_ERROR_UPDATE_STATUS_INC;
	}  
    //*****************************
	//Destroy
	//*****************************
	soap_destroy(&soap); 
	soap_end(&soap); 
	soap_done(&soap);			
	//*****************************
	return strResult;
}

