#include<stdio.h>
#include<string.h>
#include<stdlib.h>
#include<conio.h>

#define N 6
char b[N]={'1','2','3','4','5','6'};//存款密码
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


//密码验证 
void denglu()
{
  char a[20],ch; 
  int i,n,k; 
  printf("尊敬的客户，欢迎您使用ATM自动存取款机\n"); 
  do
  { 
     i=0;
     for (cs=3;cs>0;cs--)
     printf("\n请输入密码：");
  }

     while((a[i]=getch())!=13) 
   {
     i=i+1; 
     printf("*");
   } 
    for(k=0;k<N;k++) 
     if(a[k]!=b[k]) break;   
    
     if(i==N&&k==N) 
        
         printf("\n密码正确，请继续！！\n"); 
         do 
         {
          jiemian(); 
          printf("\n如需继续操作请输入y");
          ch=getch();
		 }
          while(ch=='y'); 
          
     else  
         {
          printf("\n密码错误，请重新输入：\n");
          printf("%d",cs-1);
	 }
          --cs;
     if(cs)
            {
              printf("\n您还有%d次机会，还尝试么？",cs);
              printf("\n继续请输入 y ，其他默认为退出");
              ch=getch(); 
	 }
}



			  
//修改密码
void xiugaimima()
 {
  int i=0,k;
  char m[N],ml[N];
  printf("请输入原密码：");  
  while((m[i]=getch())!=13) 
  { 
    printf("*");
    i=i+1;     
  }    
   if (i==N)  
   {  
        for(k=0;k<N;k++) 
        if(m[k]!=b[k]) 
        printf("\n原密码输入错误！！");
        break;
   } 
         
      if(k==N)
      { 
       printf("\n请输入新密码");
       for(i=0;i<N;i++)
       m[i]=getch();
       printf("*");
	  }
        
       printf("\n请再次输入新密码");
       for(i=0;i<N;i++)
       ml[i]=getch();
       printf("*");
}
          
       



void jiemian()
{
  int xuanze;
  system{"cls"};//清屏幕函数
  puts("\n*************************");
  puts("|      请选择相应功能:     |");
  puts("|  1.修改密码xiugaimima    |");
  puts("|  2.查询功能chaxun        |");
  puts("|  3.取款功能qukuan        |");
  puts("|  4.存款功能cunkuan       |");
  puts("|  5.转账功能zhuanzhang    |");
  puts("|  6.退出功能tuichu        |");
  puts("\n*************************");
  printf("\n请选择功能:");
  scanf("%d",&xuanze);
  switch(xuanze)
  {
  case 1:printf("\n修改密码功能\n");xiugaimima();break;
  case 2:printf("\n查询功能\n");chaxun();break;
  case 3:printf("\n取款功能\n");qukuan();break;
  case 4:printf("\n存款功能\n");cunkuan();break;
  case 5:printf("\n转账功能\n");zhuanzhang();break;
  case 6:printf("\n退出功能\n");Return();break;
  
  }
}

//查询功能
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


//取款功能
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
    puts("请选择取款种类:");
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
			printf("\n您的余额为%d",money);
		}

	else
		printf("\n您的余额不足!");
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
			printf("\n您的余额为%d",money);
		}

	else
		printf("\n您的余额不足!");
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
		printf("\n您的余额为%d",money);
		}
		else 
      {
	      printf("\n您的余额不足!");
          printf("\n您的余额为%d",money);
		}
          getch();
		  break;
             

 case '4': 
   puts("\n您还继续吗？ 继续请输入y，否则退出")；
         while(goon=getch()=='y');
  


//存款功能
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
			printf("\n您的余额为%d",money);
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
			printf("\n您的余额为%d",money);
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
			printf("\n您的余额为%d",money);
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
		
		printf("存款机无法处理，请核查!");
		}
		else 
              {
                 money=money+y;
                 printf("\n您的余额为%d",money);
		}
	             getch();
	             break;
				 }
			

case '5':
break;    
return;




// 转帐功能
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
               printf("\n您的余额为%d",money);
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
           printf("\n您的余额不足！");
			 }
          else 
            { 
			  money=money-500;          
              printf("\n您的余额为%d",money);
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
			  printf("\n您的余额不足！");
              money=money-1000;
              printf("\n您的余额为%d",money);
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
			  printf("\n您的余额不足！");
              printf("\n您的余额为%d",money);
		  }
              getch();
              break;
		 }

case '5': 
	break; 
      return;
         



//返回功能
   void Return()
     { 
        system("cls");
        puts("====================================");
        puts("|   Thank you for your using!       |");
        puts("|            RETURN!                |");
        puts("====================================");
        getch();
}
 

//主函数

int main()
{   
      
 denglu();
    
        
  scanf("jiemian");   
    } 
 