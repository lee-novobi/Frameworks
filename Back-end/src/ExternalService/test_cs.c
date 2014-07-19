#include "stdio.h"

#include "soapH.h"
#include "BasicHttpBinding_USCOREISDKSuportSerivces.nsmap"

int main()
{
    struct soap soap;
    struct _ns1__GetListINC tagGetListINCrequest;
    struct _ns1__GetListINCResponse tagGetListINCResponse;

    tagGetListINCrequest.sigkey = (char*)"sdk123";
  
    soap_init(&soap);
    if(soap_call___ns1__GetListINC(&soap, 
                            NULL  /*endpoint address*/, 
                            NULL  /*soapAction*/, 
                            &tagGetListINCrequest, 
                            &tagGetListINCResponse
                           )== SOAP_OK)
    {
        printf("%s\n", tagGetListINCResponse.GetListINCResult);
    }
    else
    {          
        soap_print_fault(&soap, stderr); 
    }             
        
    soap_destroy(&soap); 
    soap_end(&soap); 
    soap_done(&soap); 
                        
    return 0;                          
}
