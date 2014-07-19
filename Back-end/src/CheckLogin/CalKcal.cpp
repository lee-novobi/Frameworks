#include <iostream>
#include <stdio.h>
#include <stdlib.h>
#include <string.h>
#include <vector>

using namespace std;

int main(int argc, char* argv[])
{
	//Nam giới: BMR=[9.99 x trọng lượng cơ thể (kg)] + [6.25 x chiều cao (cm)] - [4.92 x tuổi] + 5
	//Nữ giới: BMR = [9.99 x trọng lượng cơ thể (kg)] + [6.25x chiều cao (cm)] - [4.92 x tuổi] - 161
	if(argc != 5)
		return 0;
	if(argv[1] == 0)
	{
		cout << 1.2 * ( (9.99 * atoi(argv[2])) + (6.25 * atoi(argv[3])) - (4.92 * atoi(argv[4])) + 5 ) << endl;
	}
	else
	{
		cout << 1.2 * ( (9.99 * atoi(argv[2])) + (6.25 * atoi(argv[3])) - (4.92 * atoi(argv[4])) - 161 ) << endl;
	}
	return 0;
}