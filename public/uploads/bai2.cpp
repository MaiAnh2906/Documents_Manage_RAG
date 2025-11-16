// Tran Thi Thu Thao _ 735105097 _k73e2

#include<iostream>
#include<fstream>
#include<vector>
using namespace std;

bool tontai(int x,vector<int> v){
	for(int i=0;i<v.size();i++){
		if(x==v[i]){
			return true;
			break;
		}
	}
	return false;
}

int main(){
	ifstream finp("doanhthu.inp");
	ofstream fout("doanhthu.out");
	
	if(finp.fail() || fout.fail()){
		cout<<"Khong mo duoc file";
		return 0;
	}
	int dtMT;
	finp>>dtMT;
	vector<int> dsdt;
//	int dsdt[100];
	
	int n;
	while(!finp.eof()){
		
		finp>>n;
		
		if(finp.eof()){
			break;
		}
		dsdt.push_back(n);
//		fout<<n<<" ";
	}
//	fout<<endl;
//	dong 1
	int max=0;
	for(int i=0;i<dsdt.size();i++){
		
		if(max<dsdt[i]){
			max=dsdt[i];
		}
	}
	fout<<max<<" ";
	
	int min=max;
	for(int i=0;i<dsdt.size();i++){
		if(min>dsdt[i]){
			min=dsdt[i];
		}
	}
	fout<<min<<endl;
	
//	dong 2
	int s=0;
	for(int i=0;i<dsdt.size();i++){
		
		if(dtMT<dsdt[i]){
			s+=dsdt[i];
		}
	}
	fout<<s<<endl;
	
//	dong 3
	int x=0;
	for(int i=0;i<dsdt.size()-1;i++){
		if(dsdt[i]<dsdt[i+1]){
			x++;
			
//			fout<<dsdt[i]<<" "<<dsdt[i+1]<<endl;
		}
	}
	fout<<x<<endl;
	
//	dong 4
	vector<int> dsx;
	dsx.push_back(dsdt[0]);
	for(int i=1;i<dsdt.size();i++){
		if(tontai(dsdt[i],dsx)==false){
			dsx.push_back(dsdt[i]);
		}
	}
	for(int i=1;i<dsx.size();i++){
		fout<<dsx[i]<<" ";
	}
	
	cout<<"Mo file doanhthu.out de xem ket qua";
}
