#include <stdio.h>
#include <iconv.h>
#include <string.h>

int main()
{
iconv_t it;
  size_t il=16;
  char *ibuf= new char[il];
  size_t ol=64;
  char *obuf=new char[64];
  char *toCode="UTF-8";
  char *fromCode="ISO-8859-1";

  strncpy(ibuf, "text from database", il);
  it=iconv_open(toCode, fromCode);
  if(it!=(iconv_t)-1)
  {
    size_t c;
    char *source=ibuf;
    char *result=obuf;
    il=strlen(ibuf);
    if(iconv(it, &ibuf, &il, &obuf, &ol)!=-1)
    {
      printf("%s -> %s\n", source, result);
    }
    else
    {
      printf("error iconv");
    }
    iconv_close(it);
  }
  else
  {
    printf("error iconv_open");
  }

        return 0;
}
