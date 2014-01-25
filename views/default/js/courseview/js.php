
//<script>  //quick and dirty script to persist the tree menu between page refreshes...should probably think about a better way to do this...maybe through ajax
    window.onload=function(){
        var treemenu = document.getElementsByClassName("cvmenuitem");
        var current=document.getElementsByClassName("cvcurrent")[0];
        
        var currentposition = current.id;
        var currentindent = current.name;
        for (i = currentposition; i >=0; i--) 
        {
            if (treemenu[i].checked==false&& treemenu[i].name<currentindent) 
            {
                treemenu[i].checked=true;
                currentindent--;
            }
           
            if (currentindent==0) 
            {
                break;
            }
        }
        current.checked=true;
    }
