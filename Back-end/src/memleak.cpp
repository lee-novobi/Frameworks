#include <stdlib.h>
#include <string.h>
#include <unistd.h>
#define BLOCK_SIZE ( 128 * 1024 )

int rte_steal_mem( void )
{
    char *p;

    /* Allocate memory */
    p = malloc((char*) BLOCK_SIZE);

    if( p )
    {
        /* Clear it - force real allocation */
        memset( p, 0, (char*) BLOCK_SIZE);

        return 0;
    }
    return -1;
}
int main( int argc, char *argv[] )
{
    while( 1 )
    {
        /* Call this function */
        if( rte_steal_mem() != 0 )
        {
            return 1;
        }
        /* Wait 2 seconds */
        sleep(2);
    }
    return 0;
}
