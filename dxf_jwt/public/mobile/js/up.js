    var i=0;
    function $(ID)
    {
        var obj=document.getElementById(ID);
        return obj;
    }
    function moveup(id,heigth)
    {
        this.id=id;
        $(this.id+2).innerHTML=$(this.id+1).innerHTML
        if(typeof(moveup._initialized=="undefined"))
        {
            moveup.prototype.move=function()
            {
                if($(this.id+2).offsetHeight-$(this.id).scrollTop<=0) 
                {
                    $(this.id).scrollTop-=$(this.id+1).offsetHeight;
                }
                else{ 
                    $(this.id).scrollTop++;
                } 
            }
            moveup._initialized=true;
        }
    }