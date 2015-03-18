$(document).ready(function(){
//write the confirm delete function.

$('#delete').click(function(event){
if(!confirm("are you sure you want to delete this message? all comments will be deleted too!"))
	{
		event.preventDefault();
		return false;
	}
	else
	{
		return true;
	}
})



})