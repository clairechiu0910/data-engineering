#include <stdio.h>
#include <stdlib.h>
#include <string.h>
#include <wchar.h>
#include <locale.h>

struct data{
	wchar_t *topic, *web, *content;
	int score, valid;
};

int min(const int a, const int b, const int c){
	int num = a;
	if(b < num) num = b;
	if(c < num) num = c;
	return num;
}

int cmp(const void *a, const void *b){
	return (((struct data *)b)->score) - (((struct data *)a)->score);
}

int search(const wchar_t *text, const wchar_t *pattern, const int error_accept){

	int textLen, patLen;
	textLen = wcslen(text);
	patLen = wcslen(pattern);

	int i, j, last = 1, count = 0;
	int A[patLen+2], B[patLen+2];
	for(i=0; i<patLen+2; i++) A[i] = B[i] = i;

	for(i=0; i<textLen; i++){
		for(j=1; j<=last+1; j++){
			A[j] = min(A[j-1]+1, B[j]+1, B[j-1]+(text[i]!=pattern[j-1]));
		}
		if(A[patLen] <= error_accept){
			if(A[patLen] == 0) count += patLen*3;
			else count += patLen-A[patLen];
		}
		if(A[last+1] <= error_accept && last <= patLen) last++;
		else if(A[last] > error_accept) last--;
		if(last > patLen+1){
			wprintf(L"error\n");
		}

		i++;
		if(i >= textLen) return count;

		for(j=1; j<=last+1; j++){
			B[j] = min(B[j-1]+1, A[j]+1, A[j-1]+(text[i]!=pattern[j-1]));
		}
		if(B[patLen] <= error_accept){
			if(B[patLen] == 0) count += patLen*3;
			else count += patLen-B[patLen];
	}
		if(B[last+1] <= error_accept && last <= patLen) last++;
		else if(B[last] > error_accept) last--;
		if(last > patLen+1){
			wprintf(L"error\n");
		}
	}
	return count;
}

int main(int argc, char* argv[])
{
	int i, j;
	int fileCount=0, patCount=0;
	char fileName[100] = "ettoday0.rec";
	wchar_t pattern[10][100]={0};
	setlocale(LC_ALL, "");

	for(i=1; i<argc; i++){
		if(strcmp(argv[i], "-p") == 0){
			i++;
			for( ; i<argc; i++){
				swprintf(pattern[patCount++], 100, L"%s", argv[i]);
			}
		}
	}
	//for(i=0; i<patCount; i++) wprintf(L"%ls\n", pattern[i]);

	int newsCount=0;
	wchar_t buffer[100000];
	struct data *input;
	input = (struct data *)malloc(sizeof(wchar_t *)*600000);
	FILE *fp;
	fp = fopen(fileName, "r");
	while(fgetws(buffer, 100000, fp) != 0){
		int len;
		fgetws(buffer, 100000, fp);
		len = wcslen(buffer)-1;
		buffer[len] = '\0';
		input[newsCount].web = wcsdup(buffer+3);
		fgetws(buffer, 100000, fp);
		len = wcslen(buffer)-1;
		buffer[len] = '\0';
		input[newsCount].topic = wcsdup(buffer+3);
		fgetws(buffer, 100000, fp);
		fgetws(buffer, 100000, fp);
		len = wcslen(buffer)-1;
		buffer[len] = '\0';
		input[newsCount].content = wcsdup(buffer);
		input[newsCount].valid = 1;
		newsCount++;
	}
	fclose(fp);

	for(i=0; i<newsCount; i++){
		input[i].score = 0;
		for(j=0; j<patCount; j++){
			if(pattern[j][0] == L'+'){
				if(input[i].valid == 0) break;
				int tmp = 0;
				tmp += search(input[i].topic, pattern[j]+1, 0)*100;
				tmp += search(input[i].content, pattern[j]+1, 0);
				if(tmp == 0) input[i].valid = 0;
				else input[i].score += tmp;
			}
			else if(pattern[j][0] == L'-'){
				if(input[i].valid == 0) break;
				int tmp = 0;
				tmp += search(input[i].topic, pattern[j]+1, 0)*100;
				tmp += search(input[i].content, pattern[j]+1, 0);
				if(tmp > 0) input[i].valid == 0;
			}
			else{
				if(input[i].valid == 0) break;
				input[i].score += search(input[i].topic, pattern[j], wcslen(pattern[j])/2)*100;
				input[i].score += search(input[i].content, pattern[j], wcslen(pattern[j])/2);
			}
		}
	}

	fp = fopen("output", "w");
	qsort(input, newsCount, sizeof(struct data), cmp);
	for(i=0; i<newsCount; i++){
		if(input[i].score == 0) break;
		if(input[i].valid){
			wprintf(L"@S:%d @T:%ls @W:%ls @C:%ls\n", input[i].score, input[i].topic, input[i].web, input[i].content);
		}
	}
	fclose(fp);
	free(input);
	return 0;
}
