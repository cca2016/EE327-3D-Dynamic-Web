<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" 
"http://www.w3.org/TR/html4/strict.dtd">  

<HEAD> 
<META HTTP-EQUIV="Pragma" CONTENT="no-cache"> 
<META HTTP-EQUIV="Cache-Control" CONTENT="no-cache"> 
<META HTTP-EQUIV="Expires" CONTENT="0"> 
</HEAD>
<script src="js/d3.js"></script>

<script src="js/highcharts.src.js"></script>


<script src="js/highcharts-3d.src.js"></script>

<script src="js/d3.v2.js" charset="utf-8"></script>  
<script src="js/dynamic.js"></script>
<div id="container" style="height: 400px"></div>



<input name="Type" type="checkbox" value=1 checked="true" />Author</label> 
<input name="Type" type="checkbox" value=0 checked="true"/>Paper </label> 
<input name="Type" type="checkbox" value=2 />Conference</label> 
<br/>
<input name="Explore" type="checkbox" value=3 />Explore</label> 
<button id="Reset">Reset</button>
<br/>
<label id="test" >

</label>


<style>
html,body{font-size:12px;margin:0px;height:100%;}
.mesWindow{border:#666 1px solid;background:#fff;}
.mesWindowTop{border-bottom:#eee 1px solid;margin-left:4px;padding:3px;font-weight:bold;text-align:left;font-size:12px;}
.mesWindowContent{margin:4px;font-size:12px;}
.mesWindow .close{height:15px;width:28px;border:none;cursor:pointer;text-decoration:underline;background:#fff}
</style>
<style> 
.abc a{ display:block;width:130px;height:30px;border:1px solid #000; } 
</style> 


<script type="text/javascript">

var isIe=(document.all)?true:false;
      
var currentID;
var Mode='Expand';
var hashMap = {   
    set : function(key,value){this[key] = value},   
    get : function(key){return this[key]},   
    contains : function(key){return this.get(key) == null?false:true},   
    remove : function(key){delete this[key]}   
} 
var exploreList=[];

// Give the points a 3D feel by adding a radial gradient
Highcharts.getOptions().colors = $.map(Highcharts.getOptions().colors, function (color) {
    return {
        radialGradient: {
            cx: 0.4,
            cy: 0.3,
            r: 0.5
        },
        stops: [
            [0, color],
            [1, Highcharts.Color(color).brighten(-0.2).get('rgb')]
        ]
    };
});
var renderer = new Highcharts.Renderer(
        $('#container')[0],
        400,
        400
);



// Set up the chart
var sumLink=[];
var chart = new Highcharts.Chart({
    chart: {
        table: [],
        //renderer: renderer,
        renderTo: 'container',
        margin: 100,
        type: 'scatter',
        ignoreHiddenSeries:true,

        options3d: {
            enabled: true,
            alpha: 10,
            beta: 30,
            depth: 250,
            viewDistance: 5,
            fitToPlot: false,
            frame: {
                bottom: { size: 1, color: 'rgba(0,0,0,0.02)' },
                back: { size: 1, color: 'rgba(0,0,0,0.04)' },
                side: { size: 1, color: 'rgba(0,0,0,0.06)' }
            }
        }
    },
    credits: {
            enabled: false
        },
    title: {
        text: 'Dynamic Map'
    },
    plotOptions: {
         
        scatter: {
            width: 400,
            height: 400,
            depth: 400,   
        },

       series:{
           
           point:{
                events:{
                      
                    click:function(){
                        //update();
                        for (var i=0;i<3;i++){
                            for (var j=0;j<chart.series[i].data.length;j++)
                              //  chart.series[i].data[j].color=Highcharts.Color(  chart.series[i].data[j].color).brighten(0.7).get( 'rgb') 
                             chart.series[i].data[j].color=chart.series[i].color;
                        }
                        if (chart.series.length>3){               
                         for (var j=chart.series.length-1;j>=3;j--)
                           chart.series[j].remove();
                        }

                        if (Mode=='Expand'){

                            if (this.hasExpand){
                                for (var i=0;i<3;i++){
                                for (var j=0;j<chart.series[i].data.length;j++)
                                    chart.series[i].data[j].color=Highcharts.Color(  chart.series[i].data[j].color).brighten(0.5).get( 'rgb') 
                               //  chart.series[i].data[j].color=chart.series[i].color;
                                }
                            
                                this.color=chart.series[this.type].color;
                                
                              

                                for (var t in this.links){ 
                                    var x=this.x+0.00001,
                                        y=this.y+0.00001,
                                        z=this.z+0.00001;
                                       
                                    if (!hashMap.contains(t)) continue;
                                    var id=hashMap.get(t),type;
                                    if (t[0]=='P') type=0;
                                    else if (t[0]=='A') type=1;
                                    else if (t[0]=='C') type=2;
                                    if (!chart.series[type].visible) continue;
                                    var tx=chart.series[type].data[id].x+0.00001,
                                        ty=chart.series[type].data[id].y+0.00001,
                                        tz=chart.series[type].data[id].z+0.00001;
                                    chart.series[type].data[id].color=chart.series[type].color;

                                    chart.addSeries({
                                            type:'scatter',
                                            data: [[x,y,z],[tx,ty,tz]],
                                                    
                                                  //  data:[[x,y,0],[100,200,200]],
                                            marker: {
                                                enabled: false
                                               
                                            },
                                            lineWidth:1
                                                   
                                    });


                                }
                                updateMSG(this);
                               

                            }
                            else {
                                    this.hasExpand=true;
                                    console.log("click!",this);
                                    $.ajax ({
                                        url: "dynamicmap/expand",
                                        data: {"type": this.type, "id": this.id, "index":this.index},
                                        dataType: 'json',
                                        success: function(data) {
                                           // console.log("success",data,data['refPaperID'].length,data['refAuthorID'].length,data['refConferID'].length);
                                           console.log("success",data);
                                            var fatherIndex=data['fatherIndex'];
                                            fatherIndex=parseInt(fatherIndex);
                                           // console.log("ref",data['refPaperID']);
                                            var fatherNode=chart.series[data['fatherType']].data[data['fatherIndex']];
                                            var index;
     
                                            for (var i=0;i<data['refPaper'].length;i++){
                              
                                                var name='P'+data['refPaper'][i]['PaperID'],
                                                    title=data['refPaper'][i]['OriginalPaperTitle'],
                                                    year=data['refPaper'][i]['PaperPublishYear'];

                                                console.log("Papername",name,title,year);
                                                if (hashMap.contains(name)) {
                                                    index=hashMap.get(name);
                                                 
                                                }
                                                else {
                                                     
                                                    hashMap.set(name,chart.series[0].data.length);
                                                    index=chart.series[0].data.length;
                                                    chart.series[0].addPoint({x:0,y:0,z:0,"id":name,"links":{},"type":0,"hasExpand":false,"title":title, "year":year},1);
                                                    chart.series[0].options.nodes.push({name:index});
                                                  //  console.log("add",chart.series[0].data.length,chart.series[0].options.nodes.length,chart.series[1].options.nodes.length);
                                                }
                                               //  console.log("fatherNode",fatherNode);
                                                if (fatherNode.links[name]==null){
                                                    fatherNode.links[name]=1;
                                                    chart.series[0].data[index].links[fatherNode.id]=1;
                                                    if (data['fatherType']==0)
                                                       chart.series[0].options.links.push({source:fatherIndex, target:index});
                                                }
                                              /*  fatherNode.links.push(name);
                                                chart.series[0].data[index].links.push(fatherNode.id);
                                                if (data['fatherType']==0)
                                                    chart.series[0].options.links.push({source:fatherIndex, target:index});*/
                                              
                                            }
                                            for (var i=0;i<data['refAuthor'].length;i++){

                                                var id='A'+data['refAuthor'][i]['AuthorID'];
                                                var name=data['refAuthor'][i]['AuthorName'];
                                                console.log("author",id,name);
                                                if (hashMap.contains(id)){
                                                    index=hashMap.get(id);
                                                }
                                                else {
                                                    hashMap.set(id,chart.series[1].data.length);
                                                    index=chart.series[1].data.length;
                                                    chart.series[1].addPoint({x:0,y:1000,z:0,"id":id,"links":{},"type":1,"hasExpand":false,"name":data['refAuthor'][i]['AuthorName']},1);
                                                    chart.series[1].options.nodes.push({name:index});
                                                    
                                                }
                                               //  console.log("fatherNode",fatherNode);
                                                if (fatherNode.links[id]==null){
                                                    fatherNode.links[id]=1;
                                                    chart.series[1].data[index].links[fatherNode.id]=1;
                                                    if (data['fatherType']==1)
                                                       chart.series[1].options.links.push({source:fatherIndex, target:index});
                                                }
                                              /*  fatherNode.links.push(id);
                                                chart.series[1].data[index].links.push(fatherNode.id);
                                                if (data['fatherType']==1)
                                                    chart.series[1].options.links.push({source:fatherIndex, target:index});*/
                                              
                                            }

                                            for (var i=0;i<data['refConfer'].length;i++){
                                                var id='C'+data['refConfer'][i]['ConferenceSeriesIDMappedToVenueName'];
                                                var name=data['refConfer'][i]['FullName'];
                                               // console.log("Conference",id,name);
                                                if (hashMap.contains(id)){
                                                    index=hashMap.get(id);
                                                }
                                                else {
                                                    hashMap.set(id,chart.series[2].data.length);
                                                    index=chart.series[2].data.length;
                                                    chart.series[2].addPoint({x:0,y:-1000,z:0,"id":id,"links":{},"type":2,"hasExpand":false,"name":data['refConfer'][i]['FullName']},1);
                                                    chart.series[2].options.nodes.push({name:index});
                                                }
                                            //    console.log("fatherNode",fatherNode);

                                             if (fatherNode.links[id]==null){
                                                    fatherNode.links[id]=1;
                                                    chart.series[2].data[index].links[fatherNode.id]=1;
                                                    if (data['fatherType']==2)
                                                       chart.series[2].options.links.push({source:fatherIndex, target:index});
                                                }
                                              /*  fatherNode.links.push(id);
                                                 chart.series[2].data[index].links.push(fatherNode.id);
                                               
                                                if (data['fatherType']==2)
                                                    chart.series[2].options.links.push({source:fatherIndex, target:index});*/
                                              
                                            }

                                         //   console.log("series",chart.series);
                                         //   console.log("add-end",chart.series[0].data.length,chart.series[0].options.nodes.length);
                                            var force=layout()
                                            .nodes(chart.series[0].options.nodes)
                                            .links(chart.series[0].options.links)
                                            .size([0,0])
                                            .linkDistance(150)
                                            .charge(-400);
                                           
                                            force.start();
                                                    
                                            var force1=layout()
                                              .nodes(chart.series[1].options.nodes)
                                                    .links(chart.series[1].options.links)
                                                    .size([0,0])
                                                    .linkDistance(150)
                                                    .charge(-400);
                                            force1.start();

                                            var force2=layout()
                                              .nodes(chart.series[2].options.nodes)
                                                    .links(chart.series[2].options.links)
                                                    .size([0,0])
                                                    .linkDistance(150)
                                                    .charge(-400);
                                            force2.start();
                                          //  console.log("end");

                                            
                                            for (var k=0;k<3;k++)
                                                for (var j=0;j<chart.series[k].options.nodes.length;j++){
                                           
                                                 //   console.log("update",k,j); 
                                                    chart.series[k].data[j].x=chart.series[k].options.nodes[j].x;
                                                    chart.series[k].data[j].z=chart.series[k].options.nodes[j].y;                                           
                                                }
                                         //   console.log("fatheNodeLinks",fatherNode.links);
                                            updateMSG(fatherNode);
                                            chart.redraw();

                                        }

                            

                                    });
                                         
                            }
                        }
                        else {
                            if (exploreList.length==0){

                               for (var t in this.links){ 
                                    var x=this.x+0.00001,
                                        y=this.y+0.00001,
                                        z=this.z+0.00001;
                                       
                                    if (!hashMap.contains(t)) continue;
                                    var id=hashMap.get(t),type;
                                    if (t[0]=='P') type=0;
                                    else if (t[0]=='A') type=1;
                                    else if (t[0]=='C') type=2;
                                    if (!chart.series[type].visible) continue;
                                    var tx=chart.series[type].data[id].x+0.00001,
                                        ty=chart.series[type].data[id].y+0.00001,
                                        tz=chart.series[type].data[id].z+0.00001;
                                    chart.series[type].data[id].color=chart.series[type].color;

                                    chart.addSeries({
                                            type:'scatter',
                                            data: [[x,y,z],[tx,ty,tz]],
                                            marker: {
                                                enabled: false,
                                                radius:8
                                            },
                                            lineWidth:1
                                                   
                                    });


                                }

                            }
                            else {
                                var aim={};
                                for (var i=0;i<exploreList.length;i++){
                                    aim[exploreList[i]]=1;
                                }
                                var num=0;
                                var front=0,end=1,a=[],flag={},connect={};
                                a[0]=this.id;
                                flag[this.id]=true;
                              
                                while ((num<exploreList.length) && (front<end)){
                                    console.log("while",front,end,a)
                                    var t=a[front];
                                    var id=hashMap.get(t),type;
                                    if (t[0]=='P') type=0;
                                    else if (t[0]=='A') type=1;
                                    else if (t[0]=='C') type=2;
                                    var node=chart.series[type].data[id];

                                    for (var p in node.links){
                                        if (!flag[p]){
                                            if (aim[p]==1){
                                                num++;
                                                aim[p]=2;
                                            }
                                            flag[p]=true;
                                            connect[p]=t;

                                            a[end]=p;
                                            end++;    
                                        }
                                    }
                                    front++;
                                  //  flag[t]=false;
                                }

                                for (var t in aim){
                                    if (aim[t]==2){
                                        var p=t;
                                        while (p!=this.id){

                                            addLine(p,connect[p]);
                                            console.log("addline",p,connect[p]);
                                            p=connect[p];
                    
                                        }
                                    }
                                }
                            }
                            exploreList.push(this.id);
                            chart.redraw();
 




                        }   
                      

                     console.log(this);
                     //   location.href="/papermap/pos2paper";
                    }
                }
            }
         }  

    },
    yAxis: {
        min: -1000,
        max: 1000,
        title: null,
        visible:true

    },
    xAxis: {
        min: -700,
        max: 700,
        gridLineWidth: 1,
        visible:true
    },
    zAxis: {
        min: -700,
        max: 700,
        showFirstLabel: false,
        visible:true

    },
    legend: {
        enabled: false
    },
    tooltip:{
        useHTML: true,
        followTouchMove:false,
        stickyTracking:false,
        
       // shared: true,
        style: {
            padding: 0,
             fontSize: "10px",
            pointerEvents: 'auto'
        },
        formatter: function(){
       //   console.log("thos",this.point.id);
            var id=this.point.id.substr(1);
            currentID=this;
            if (this.point.type==0){
                return 'Title: '+'<a href="http://acemap.sjtu.edu.cn/paper/paperpage?PaperID='+id+'" target="_blank">'+this.point.title+', '+this.point.year+'</a> <br/>'
               // +'<a onclick="locateAuthor()">'
                +'<a  onclick="testMessageBox(event)">Abstract</a>';
            } 
            else if (this.point.type==1){
                return 'Author: '+'<a href="http://acemap.sjtu.edu.cn/author/page?AuthorID='+id+'" target="_blank">'+this.point.name+'</a> <br/>';
               // +'<a onclick="locateAuthor()">'
               

            }
            else if (this.point.type==2){
                return 'Conference: '+this.point.Fullname+'</a> <br/>';

            }
        
           
        }
        
    },

    
    series: create(),

});

function addLine(p,q){

    if (!hashMap.contains(p) || !hashMap.contains(q)) return;
    var id1=hashMap.get(p),type1;
    if (p[0]=='P') type1=0;
    else if (p[0]=='A') type1=1;
    else if (p[0]=='C') type1=2;
    var x=chart.series[type1].data[id1].x+0.00001,
        y=chart.series[type1].data[id1].y+0.00001,
        z=chart.series[type1].data[id1].z+0.00001;

    var id2=hashMap.get(q),type2;
    if (q[0]=='P') type2=0;
    else if (q[0]=='A') type2=1;
    else if (q[0]=='C') type2=2;
    var tx=chart.series[type2].data[id2].x+0.00001,
        ty=chart.series[type2].data[id2].y+0.00001,
        tz=chart.series[type2].data[id2].z+0.00001;
                                                                                                                   
    chart.series[type1].data[id1].color=chart.series[type1].color;
    chart.series[type2].data[id2].color=chart.series[type2].color;

    chart.addSeries({
        type:'scatter',
        data: [[x,y,z],[tx,ty,tz]],
        marker: {
            enabled: false
        },
        lineWidth:1                                                         
    });

}
function setSelectState(state)
{
    var objl=document.getElementsByTagName('select');
    for(var i=0;i<objl.length;i++)
    {
        objl[i].style.visibility=state;
    }
}
function showBackground(obj,endInt)
{
    if(isIe)
    {
        obj.filters.alpha.opacity+=1;
        if(obj.filters.alpha.opacity<endInt)
        {
            setTimeout(function(){showBackground(obj,endInt)},5);
        }
    }else{
        var al=parseFloat(obj.style.opacity);al+=0.01;
        obj.style.opacity=al;
        if(al<(endInt/100))
        {
            setTimeout(function(){showBackground(obj,endInt)},5);
        }
    }
}
function closeWindow()
{
    if(document.getElementById('back')!=null)
    {
        document.getElementById('back').parentNode.removeChild(document.getElementById('back'));
    }
    if(document.getElementById('mesWindow')!=null)
    {
        document.getElementById('mesWindow').parentNode.removeChild(document.getElementById('mesWindow'));
    }
    if(isIe) setSelectState('');
    
}

function mousePosition(ev)
{
    if(ev.pageX || ev.pageY)
    {
        return {x:ev.pageX, y:ev.pageY};
    }
    return {
        x:ev.clientX + document.body.scrollLeft - document.body.clientLeft,y:ev.clientY + document.body.scrollTop - document.body.clientTop
    };
}

function showMessageBox(wTitle,content,pos,wWidth)
{
    closeWindow();
    var bWidth=parseInt(document.documentElement.scrollWidth);
    var bHeight=parseInt(document.documentElement.scrollHeight);
    if(isIe){
        setSelectState('hidden');}
    var back=document.createElement("div");
    back.id="back";
    var styleStr="top:0px;left:0px;position:absolute;background:#666;width:"+bWidth+"px;height:"+bHeight+"px;";
    styleStr+=(isIe)?"filter:alpha(opacity=0);":"opacity:0;";
    back.style.cssText=styleStr;
    document.body.appendChild(back);
    showBackground(back,50);
    var mesW=document.createElement("div");
    mesW.id="mesWindow";
    mesW.className="mesWindow";
    mesW.innerHTML="<div class='mesWindowTop'><table width='100%' height='100%'><tr><td>"+wTitle+"</td><td style='width:1px;'><input type='button' onclick='closeWindow();' title='关闭窗口' class='close' value='关闭' /></td></tr></table></div><div class='mesWindowContent' id='mesWindowContent'>"+content+"</div><div class='mesWindowBottom'></div>";
    styleStr="left:"+(((pos.x-wWidth)>0)?(pos.x-wWidth):pos.x)+"px;top:"+(pos.y)+"px;position:absolute;width:"+wWidth+"px;";
    mesW.style.cssText=styleStr;
    document.body.appendChild(mesW);
}

function testMessageBox(ev)
{

    console.log("out",ev);
    var objPos = mousePosition(ev);
    console.log("hh",currentID.point.id.substr(1));
    $.get("dynamicmap/queryAbstract?PaperID="+currentID.point.id.substr(1), function(json){
        var resp = JSON.parse(json);
        console.log("dsad",resp.Abstract);  
        showMessageBox('Abstract',resp.Abstract,objPos,350);
    });

}

function create(){

    var series =new Array();
    var arr=<?php echo json_encode($result['response']['docs']);?>;
    var aarr=eval(arr);
    var nodes=[],nodes1=[],nodes2=[];
    var links=[];
    var data=[];
    series.lineWidth=1;
    for ( var i = 0; i <1 ; i ++ ) {
       // var data=[];
        var id=[],tx=Math.random()*100,tz=Math.random()*100;
     //   id.push(aarr[i]['id']);
        
        data.push({x:tx,y:0,z:tz,"id":'P44469F36',"type":0,"hasExpand":false,"links":[]});
        nodes.push({name:i});
      //  hashMap.set('P'+aarr[i]['id'],i);
      hashMap.set('P44469F36',i);
    }
     hashMap.set('A777F0A79',0);
     hashMap.set('C42C0AB71',0);
console.log("hashMap",hashMap);
 nodes1.push({name:0});
 nodes2.push({name:0});
    var data1=[];
    data1.push({x:tx,y:500,z:tz,"id":'A777F0A79',"type":1,"hasExpand":false,"links":[],"name":'Nancy'});
    var data2=[];
    data2.push({x:tx,y:-500,z:tz,"id":'C42C0AB71',"type":2,"hasExpand":false,"links":[],"name":'Nancy'});

    series.push({"type":'scatter',"data":data,"links":links,"nodes":nodes});
    series.push({"type":'scatter',"data":data1,"links":[],"nodes":nodes1});
    series.push({"type":'scatter',"data":data2,"links":[],"nodes":nodes2});
   
    return series;
}

function updateMSG(p){
    obj_label = document.getElementById("test");
    var str="";
    var refStr="";
    var AuthorStr="";
    console.log("qqqqqqqqqq",p);
    if (p.type==0){

        str='<a  onclick="choosePoint(\''+p.id+'\')">'+p.title+'</a> , '+p.year;
        console.log("1kkkkkkkk",str);
       
        for (var nodeID in p.links) {
       // for (var i=0;i<p.links.length;i++){
           
          //  var nodeID=p.links[i];
            console.log("nodeID",nodeID,hashMap);
            var index=hashMap.get(nodeID),node;
            console.log("INDEX",index,chart);

           
            if (nodeID[0]=='P'){
                
                node=chart.series[0].data[index];
                console.log("node",node);
                refStr=refStr+'*<a  onclick="choosePoint(\''+node.id+'\')">'+node.options.title+'</a> , '+node.options.year+'<br/>';
                console.log("refStr",refStr);
                   
                    
                

            }
            else if (nodeID[0]=='A'){
                   if (AuthorStr!="") AuthorStr+=', ';
                    console.log("node",node);
                    node=chart.series[1].data[index];
                    AuthorStr=AuthorStr+'<a  onclick="choosePoint(\''+node.id+'\')">'+node.options.name+'</a>';
                   
                
            }
        }
        obj_label.innerHTML=str+'<br/>'+'Author:<br/>'+AuthorStr+'<br/>'+'Reference:<br/>'+refStr;
      
   }
   else if (p.type==1){
        str='<a  onclick="choosePoint(\''+p.id+'\')">'+p.name+"</a>";
        for (var nodeID in p.links) {
      //  for (var i=0;i<p.links.length;i++){
           
           // var nodeID=p.links[i];
            var index=hashMap.get(nodeID),node;
           
            if (nodeID[0]=='P'){
                
                    node=chart.series[0].data[index];
                refStr=refStr+'*<a  onclick="choosePoint("'+node.id+'")">'+node.options.title+"</a> , "+node.options.year+"<br/>";
              //  refStr=refStr+'<a  onclick="choosePoint(\''+node.id+'\')">';
                console.log(refStr);

                   
                    
                

            }
            else if (nodeID[0]=='A'){
                   if (AuthorStr!="") AuthorStr+=', ';
                  
                    node=chart.series[1].data[index];
                    AuthorStr=AuthorStr+'<a  onclick="choosePoint(\''+node.id+'\')">'+node.options.name+"</a>";
                   
                
            }
        }

        obj_label.innerHTML='Author'+str+'<br/>'+'Paper:<br/>'+refStr+'<br/>'+'Co-author:<br/>'+ AuthorStr;

       console.log(obj_label.innerHTML);
   }

}


function choosePoint(obj){
    var type,index;
    console.log("obj",obj);
    if (obj[0]=='P') type=0;
    else if (obj[0]=='A') type=1;
    else type=2;

    if (!hashMap.contains(obj)) return;
    index=hashMap.get(obj);
    if (chart.series.length>3){               
        for (var j=chart.series.length-1;j>=3;j--)
            chart.series[j].remove();
    }
    for (var i=0;i<3;i++)
        for (var j=0;j<chart.series[i].data.length;j++)
          chart.series[i].data[j].color=Highcharts.Color(chart.series[i].color).brighten(0.5).get( 'rgb');
    chart.series[type].data[index].color=chart.series[type].color;
    chart.redraw();
}
/*
function update(){
    obj_label = document.getElementById("test");
    obj_label.innerHTML=chart.series[0].data.length+"author:"+'<a  onclick="choosePoint()">弹出窗口</a>';
}*/

$(function(){
    var s = $("input[name='Type']");
    s.each(function(i) {
//          alert(i);
            $(this).click(function(){
                console.log("click");
                if(this.checked==true){
                      chart.series[this.value].show();
                }
                else chart.series[this.value].hide();
                chart.pointer.reset();
                chart.pointer.chartPosition = null;

                for (var i=0;i<3;i++){
                    for (var j=0;j<chart.series[i].data.length;j++)                            
                        chart.series[i].data[j].color=chart.series[i].color;
                }
                if (chart.series.length>3){               
                    for (var j=chart.series.length-1;j>=3;j--)
                        chart.series[j].remove();
                }
             });
        }); 
})

$(function(){
    var s = $("input[name='Explore']");
    s.each(function(i) {
//          alert(i);
            $(this).click(function(){
                console.log("click");
                if(this.checked==true){
                      Mode='Explore';
                      exploreList=[];
                }
                else  Mode='Expand';
                chart.pointer.reset();
                chart.pointer.chartPosition = null;

                for (var i=0;i<3;i++){
                    for (var j=0;j<chart.series[i].data.length;j++)                            
                        chart.series[i].data[j].color=chart.series[i].color;
                }
                if (chart.series.length>3){               
                    for (var j=chart.series.length-1;j>=3;j--)
                        chart.series[j].remove();
                }
             });
        }); 
})
// Add mouse events for rotation

 
$('#Reset').click(function(){
    console.log("refresh");
    if (Mode=='Explore'){
        chart.pointer.reset();
        chart.pointer.chartPosition = null;

        for (var i=0;i<3;i++){
            for (var j=0;j<chart.series[i].data.length;j++)                            
                chart.series[i].data[j].color=chart.series[i].color;
        }
        if (chart.series.length>3){               
            for (var j=chart.series.length-1;j>=3;j--)
                chart.series[j].remove();
        }
        exploreList=[];
    }

});
$(chart.container).on('mousedown.hc touchstart.hc', function (eStart) {
    eStart = chart.pointer.normalize(eStart);
    chart.tooltip.hide();
    var posX = eStart.pageX,
        posY = eStart.pageY,
        alpha = chart.options.chart.options3d.alpha,
        beta = chart.options.chart.options3d.beta,
        newAlpha,
        newBeta,
        sensitivity = 5; // lower is more sensitive
    
    $(document).on({
        'mousemove.hc touchdrag.hc': function (e) {
            // Run beta
            newBeta = beta + (posX - e.pageX) / sensitivity;
            chart.options.chart.options3d.beta = newBeta;

            // Run alpha
            newAlpha = alpha + (e.pageY - posY) / sensitivity;
            chart.options.chart.options3d.alpha = newAlpha;
             

            chart.redraw(false);
        },
        'mouseup touchend': function () {
            $(document).off('.hc');
        }
    });
}); 

        </script>
    </body>
</html>
