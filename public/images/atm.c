#include<stdio.h>
#include<string.h>
#include<stdlib.h>
#include<conio.h>

#define N 6
char b[N]={'1','2','3','4','5','6'};//�������
int money=5000;
int cs=3;
void denglu();
void jiemian();
void xiugaimima();
void cunkuan();
void qukuan();
void chaxun();
void zhuanzhang();
void Return();return;


//������֤ 
void denglu()
{
  char a[20],ch; 
  int i,n,k; 
  printf("�𾴵Ŀͻ�����ӭ��ʹ��ATM�Զ���ȡ���\n"); 
  do
  { 
     i=0;
     for (cs=3;cs>0;cs--)
     printf("\n���������룺");
  }

     while((a[i]=getch())!=13) 
   {
     i=i+1; 
     printf("*");
   } 
    for(k=0;k<N;k++) 
     if(a[k]!=b[k]) break;   
    
     if(i==N&&k==N) 
        
         printf("\n������ȷ�����������\n"); 
         do 
         {
          jiemian(); 
          printf("\n�����������������y");
          ch=getch();
		 }
          while(ch=='y'); 
          
     else  
         {
          printf("\n����������������룺\n");
          printf("%d",cs-1);
	 }
          --cs;
     if(cs)
            {
              printf("\n������%d�λ��ᣬ������ô��",cs);
              printf("\n���������� y ������Ĭ��Ϊ�˳�");
              ch=getch(); 
	 }
}



			  
//�޸�����
void xiugaimima()
 {
  int i=0,k;
  char m[N],ml[N];
  printf("������ԭ���룺");  
  while((m[i]=getch())!=13) 
  { 
    printf("*");
    i=i+1;     
  }    
   if (i==N)  
   {  
        for(k=0;k<N;k++) 
        if(m[k]!=b[k]) 
        printf("\nԭ����������󣡣�");
        break;
   } 
         
      if(k==N)
      { 
       printf("\n������������");
       for(i=0;i<N;i++)
       m[i]=getch();
       printf("*");
	  }
        
       printf("\n���ٴ�����������");
       for(i=0;i<N;i++)
       ml[i]=getch();
       printf("*");
}
          
       



void jiemian()
{
  int xuanze;
  system{"cls"};//����Ļ����
  puts("\n*************************");
  puts("|      ��ѡ����Ӧ����:     |");
  puts("|  1.�޸�����xiugaimima    |");
  puts("|  2.��ѯ����chaxun        |");
  puts("|  3.ȡ���qukuan        |");
  puts("|  4.����cunkuan       |");
  puts("|  5.ת�˹���zhuanzhang    |");
  puts("|  6.�˳�����tuichu        |");
  puts("\n*************************");
  printf("\n��ѡ����:");
  scanf("%d",&xuanze);
  switch(xuanze)
  {
  case 1:printf("\n�޸����빦��\n");xiugaimima();break;
  case 2:printf("\n��ѯ����\n");chaxun();break;
  case 3:printf("\nȡ���\n");qukuan();break;
  case 4:printf("\n����\n");cunkuan();break;
  case 5:printf("\nת�˹���\n");zhuanzhang();break;
  case 6:printf("\n�˳�����\n");Return();break;
  
  }
}

//��ѯ����
void chaxun()
{
system("cls");
puts("*************************");
printf("|  Your chaxun balance is $%ld    |\n",money);
puts("|   Press any key to return*****    |");
puts("*************************");
getch();
return;
}


//ȡ���
void qukuan()
{
   char qukuan;
   long y;
   do
   {
   //system("cls");
    puts("*************************");
	puts("|   qing xuanze qukuan:   |");
	puts("|   1.$100                |");
	puts("|   2.$200                |");
	puts("|   3.$other              |");
	puts("|   4.Return              |");
	puts("*************************");
    puts("��ѡ��ȡ������:");
    qukuan=getch();
   }
	while(qukuan!='1'&&qukuan!='2'&&qukuan!='3'&&qukuan!='4');
		switch(qukuan)
	{
case '1':
		system("cls");
		if(money>100)
		{
			puts("*************************");
		    puts("|      ni qukuan shi $100,Thank you!   |");
			puts("|      Press any key to return---      |");
			puts("*************************");
			money=money-100;
			printf("\n�������Ϊ%d",money);
		}

	else
		printf("\n��������!");
	}
	    getch();
		break;
}



case '2':
		system("cls");
		if(money>200)
		{
			puts("*************************");
		    puts("|      ni qukuan shi $200,Thank you!   |");
			puts("|      Press any key to return---      |");
			puts("*************************");
			money=money-200;
			printf("\n�������Ϊ%d",money);
		}

	else
		printf("\n��������!");
}
	    getch();
		break;




case '3':
		system("cls");
		printf("qing qukuan:");
		scanf("%ld",&y);
	    if(money>y)
		{
	    money=money-y;
		printf("\n�������Ϊ%d",money);
		}
		else 
      {
	      printf("\n��������!");
          printf("\n�������Ϊ%d",money);
		}
          getch();
		  break;
             

 case '4': 
   puts("\n���������� ����������y�������˳�")��
         while(goon=getch()=='y');
  


//����
void cunkuan()

{
char cunkuan;
   long y;
   do
   {
   //system("cls");
    puts("*************************");
	puts("|   qing xuanze cunkuan:  |");
	puts("|   1.$100                |");
    puts("|   2.$500                |");
	puts("|   3.$1000               |");
	puts("|   4.$other              |");
	puts("|   5.Return              |");
	puts("*************************");
       cunkuan=getch();
   }
	while(cunkuan!='1'&&cunkuan!='2'&&cunkuan!='3'&&cunkuan!='4'&&cunkuan!='5');

         switch(cunkuan)
	{

case '1':
		{
			system("cls");
		if(money>100)
		{
			puts("*************************");
		    puts("|      Sorry,nide yue buzu $100!    |");
	        puts("|      Press any key to return---   |");
		    puts("*************************");
	
			money=money+100;
			printf("\n�������Ϊ%d",money);
		}

	                getch();
	                break;
		}

         
case '2':
		{
			system("cls");
		
			puts("*************************");
		    puts("|      ni cunkuan shi $500,Thank you!  |");
			puts("|      Press any key to return---      |");
			puts("*************************");}
			
            money=money+500;
			printf("\n�������Ϊ%d",money);
		 }
  
	                getch();
	                break;


case '3':
		{
			system("cls");
		
			puts("*************************");
		    puts("|      ni cunkuan shi $1000,Thank you! |");
			puts("|      Press any key to return---      |");
			puts("*************************");}
			
            money=money+1000;
			printf("\n�������Ϊ%d",money);
}

	                getch();
	                break;


case '4':
		{
			system("cls");
		
			puts("*************************");
		    puts("|     qing cukuan,Thank you!     |");
			puts("|    Press any key to return---  |");
			puts("*************************");}
            puts("qing cunkuan:");
            scanf("%ld",&y);
	   
			if(y%100!=0)
		
		printf("�����޷�������˲�!");
		}
		else 
              {
                 money=money+y;
                 printf("\n�������Ϊ%d",money);
		}
	             getch();
	             break;
				 }
			

case '5':
break;    
return;




// ת�ʹ���
 void zhuanzhang()
 {
  char zhuanzhang;
  long y;
  do
      {//system("cls");
  
       puts("==========================================");
       puts("|   Please select zhuanzhang shumu:      |");
       puts("|   1. $100                              |");
       puts("|   2. $500                              |");
       puts("|   3. $1000                             |");
       puts("|   4. other                             |");
       puts("|   5. Return                            |");
       puts("==========================================");
      zhuanzhang = getch();
	  }
   while(zhuanzhang!='1'&&zhuanzhang!='2'&& zhuanzhang!='3'&&zhuanzhang!='4'&&zhuanzhang!='5');
       switch(zhuanzhang)

       {
 case '1':
        {
			if(money<100)
         {
				system("cls");
          puts("===========================================");
          puts("|   Sorry,Your balance not enough $100!   |");
          puts("|   Press any key to return...            |");
          puts("===========================================");
			}
          else
		  {
			  money=money-100;
               printf("\n�������Ϊ%d",money);
		}
               getch();
               break;
	   }

case '2':
         {
			 if(money<500)
          {
				 system("cls");
           puts("============================================");
           puts("|   Your zhuanzhang is $500,Thank you!    |");
           puts("|   Press any key to return...            |");
           puts("============================================");
           printf("\n�������㣡");
			 }
          else 
            { 
			  money=money-500;          
              printf("\n�������Ϊ%d",money);
		  }
              getch();
              break;}

case '3':
         {
			 system("cls");
          puts("===========================================");
          puts("|   Your zhuanzhang shumu is $1000,Thank you!   |");
          puts("===========================================");
          if(money>y)
             money=money-1000;
          else 
		  {
			  printf("\n�������㣡");
              money=money-1000;
              printf("\n�������Ϊ%d",money);
              puts("|   Press any key to return...             |"); 
		  }
              getch();
              break;
		 }

case '4':
         {
			 system("cls"); 
          puts("============================================");
          puts("|   Your zhuan yixie zhang,Thank you!       |");
          puts("|   Press any key to return...              |");
          puts("============================================");
          puts("please save some money:");
          scanf("%ld",&y);
          if(money>y)
             money=money-y;
          else 
		  {
			  printf("\n�������㣡");
              printf("\n�������Ϊ%d",money);
		  }
              getch();
              break;
		 }

case '5': 
	break; 
      return;
         



//���ع���
   void Return()
     { 
        system("cls");
        puts("====================================");
        puts("|   Thank you for your using!       |");
        puts("|            RETURN!                |");
        puts("====================================");
        getch();
}
 

//������

int main()
{   
      
 denglu();
    
        
  scanf("jiemian");   
    } 
 