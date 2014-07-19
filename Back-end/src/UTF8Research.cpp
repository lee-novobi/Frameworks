#include "./Common/Common.h"

char* Hex(const char* strText);
int main()
{
	string strTx = "Cái nồi gì thế";
	Hex(strTx.c_str());
}

char* Hex(const char* strText)
{
	string strRes;
	for(int i = 0; i < strlen(strText); i++)
	{
		uint8_t ch = strText[i];
		if(ch < 0x80) {
			// append(ch);
			strRes.append((char*)ch);
		} else {
			// append(0xc0 | (ch & 0xc0) >> 6); /* first byte, simplified since our range is only 8-bits */
			// append(0x80 | (ch & 0x3f));
			strRes.append((char*)ch);
			strRes.append((char*)(0xc0 | (ch & 0xc0) >> 6));
			strRes.append((char*)(0x80 | (ch & 0x3f)));
		}
		cout << ch;
	}
	cout << endl;
}