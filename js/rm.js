jQuery(document).ready( function() {

jQuery('#pRdown').click(function(){ 
  jQuery('#message #d-message').show();
  jQuery('#message #c-message').hide();
          Ajaxgetfrst();
   });

jQuery('#trkDown').click(function(){ 
  jQuery('.orders-list #d-message').fadeIn(2000);
  jQuery('.orders-list .saveTrackNumbers').fadeOut(4000);
    RunTrackNumberFunc();  
   });

jQuery('#Cdown').click(function(){ 
          Ajaxgetsec();
   });    
function Ajaxgetfrst(){
    data ={'action':'export_todays_orders_for_royalmail','security':WP.NONCE}
    console.log("||-----Ajaxgetfrst()___Started");
    jQuery.post(ajaxurl, data, function(response) {
    //alert('Got this from the server: ' + response);
    console.log("||-----Ajaxgetfrst()___Started");
    JSONToCSVConvertor(response, "Orders", true);
   });
}
function Ajaxgetsec(){
          
   data ={'action':'AJXp7o8iFGH789fnfid93bdj0a1','security':WP.CNONCE}

   jQuery.post(ajaxurl, data, function(response) {
       //alert('Got this from the server: ' + response);
       //jQuery('#resTxt').html(response);
       JSONToCSVConvertor(response, "customers", true);
   });
}
function RunTrackNumberFunc(){
      var myArray1 = [];
      var myArray2 = [];
      jQuery('.actTrackingNumberInput').each(function () { myArray1.push(jQuery(this).val()); myArray2.push(jQuery(this).attr('rel'));});
      var myArray1L = myArray1.length;
      var myArray2L = myArray2.length;
      if(myArray1L != myArray2L){
        alert('Error getting 2data from fields. You may have to do this process manually please contact Jiger');
        return false;
      }else{
        data ={'action':'saveTrackDetails','security':WP.RMNONCE,'tnumbers':myArray1 ,'onmubers':myArray2}
      console.log('In');
      }
   jQuery.post(ajaxurl, data, function(response) {
   }).done(function(response) {
    alert(response);
      console.log(response);
      console.log(myArray1 + myArray2);
      //jQuery('.orders-list #d-message').hide();
      //jQuery('.orders-list #success-msg').show();

      jQuery('.orders-list #success-msg').show(1000);
  })
  .fail(function(response) {alert( "error" +response);})
  .always(function(response) {});
}

});

function RunTrackNumberFunc1(){
  //Button is pressed to save data....
  //get all data
      var Arr1 = [];//tracking numbers
      var Arr2 = [];//order numbers
      jQuery('.actTrackingNumberInput').each(function () { 
        Arr1.push(jQuery(this).val()); 
        Arr2.push(jQuery(this).attr('rel'));
      });
      var Arr1L = Arr1.length;
      var Arr2L = Arr2.length;
      if(Arr1L != Arr2L){
        alert('Error getting data from fields. You may have to do this process manually please contact Jiger');
        return false;
      }else{
        runloopForEachDatatoBeSaved(Arr1,Arr2);
      }

}
function runloopForEachDatatoBeSaved(){
      jQuery('.actTrackingNumberInput').each(function () { 
        var tn = jQuery(this).val(); 
        var on = jQuery(this).attr('rel');
        //send data
        data ={'action':'saveOrdersAndComplete','security':WP.RMNONCE,'tnumber':tn ,'onumber':on}
        if(tn != ""){var k = ajaxcallM(data);}
        else{showInfo(on,"No Tracking Number!","red","white");}
        //check data
        console.log(k);

      });
}


function ajaxcallM(data){
  var j = "";
  jQuery.post(ajaxurl, data, function(response) {})
  .done(function(response) {
      console.log(response);
      j = response;
    })
    .fail(function(response) {
      j= "error" +response;});
  return j;
}
//End of Version 2 of save data

function showInfo(h,l,c,f){
 //  res-'.$order_id.'"
 var w = "#res-"+h;
       jQuery(w).text(l).css("background-color",c).css("color",f).css("padding","3px");
}

function JSONToCSVConvertor(JSONData, ReportTitle, ShowLabel) {
    //If JSONData is not an object then JSON.parse will parse the JSON string in an Object
    var arrData = typeof JSONData != 'object' ? JSON.parse(JSONData) : JSONData;
    
    var CSV = '';    
    //Set Report title in first row or line

    //This condition will generate the Label/Header
    if (ShowLabel) {
        var row = "";
        
        //This loop will extract the label from 1st index of on array
        for (var index in arrData[0]) {
            
            //Now convert each value to string and comma-seprated
            row += index + ',';
        }

        row = row.slice(0, -1);
    }
    
    //1st loop is to extract each row
    for (var i = 0; i < arrData.length; i++) {
        var row = "";
        
        //2nd loop will extract each column and convert it in string comma-seprated
        for (var index in arrData[i]) {
            row += '"' + arrData[i][index] + '",';
        }

        row.slice(0, row.length - 1);
        
        //add a line break after each row
        CSV += row + '\r\n';
    }

    if (CSV == '') {        
        alert("Invalid data");
        return;
    }   
    
    //Generate a file name
    var today = new Date();
    var dd = today.getDate();
    var mm = today.getMonth()+1; //January is 0!
    var yyyy = today.getFullYear();
    if(dd<10) {
        dd='0'+dd
    } 
    if(mm<10) {
        mm='0'+mm
    } 
    today = mm+'-'+dd+'-'+yyyy;
    var fileName = "HH-"+today+"-Orders";
    //this will remove the blank-spaces from the title and replace it with an underscore
    fileName += ReportTitle.replace(/ /g,"_");   
    
    //Initialize file format you want csv or xls
    var uri = 'data:text/csv;charset=utf-8,' + escape(CSV);
    var link = document.createElement("a");    
    link.href = uri;
    
    //set the visibility hidden so it will not effect on your web-layout
    link.style = "visibility:hidden";
    link.download = fileName + ".csv";
    
    //this part will append the anchor tag and remove it after automatic click
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);

      jQuery('#message #d-message').hide();
      jQuery('#message #success-msg').show();
      jQuery('#message #c-message').show();
      jQuery('#message #success-msg').fadeOut(10000);
}