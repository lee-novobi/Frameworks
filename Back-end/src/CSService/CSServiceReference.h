#include <string>

#ifndef CSServiceReference_H__
#define CSServiceReference_H__

#define CODE_ERROR_SSL_INIT "1"
#define MSG_ERROR_SSL_INIT "Cannot init ssl to dc service"

#define CODE_ERROR_CRYPTO_THREAD_SETUP "2"
#define MSG_ERROR_CRYPTO_THREAD_SETUP "Cannot setup thread mutex for OpenSSL"

#define CODE_ERROR_INIT "3"
#define MSG_ERROR_INIT "Cannot access to CS service"

#define CODE_ERROR_SSL_CLIENT_CONTEXT "4"
#define MSG_ERROR_SSL_CLIENT_CONTEXT "SSL client authentication fail"

#define CODE_ERROR_UPDATE_STATUS_INC "5"
#define MSG_ERROR_UPDATE_STATUS_INC "Cannot call update status inc to CS"

// #define SECKEY "sdk123"
#define SECKEY "V34qG36hWwRxbff2ZeGH"

extern std::string CallUpdateStatusCSINC(char *pINCCode, short *pINCStatusID, char *pITSMCode, char *pCreatedBy, char *pComment, char *pITSMCloseDate);
#endif