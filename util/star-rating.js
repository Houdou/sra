$(document).ready(function(){
    for(var i = 1; i <= 5; i++){
        var starDiv = document.getElementById('star' + i);
        
        starDiv.onmouseover = function()
        { 
            setStar(this.id.substr(4));
        };
        
        starDiv.onclick = function()
        {
            document.getElementById('starValue').innerHTML = this.id.substr(4);
            setStar(this.id.substr(4));
            crForm.rate.value = parseInt(this.id.substr(4));
            console.log(crForm.rate.value);
        };
        
        starDiv.onmouseout = function()
        {
            var starValue = document.getElementById('starValue').innerHTML;
            if(starValue == "")
                return;
            else
                setStar(starValue);
        };
    }
});

function setStar(number)
{
    for(var i = 1; i <= number; i++)
        document.getElementById('star' + i).src = "img/star-on.png";
    for(var i = number; i < 5;)
        document.getElementById('star' + (++i)).src = "img/star-off.png";
}