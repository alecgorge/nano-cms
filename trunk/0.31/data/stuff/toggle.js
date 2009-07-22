// Toggle class
// by Kalyan
// used in Show pages and options for collapsing the menus

function close_element( ele ) {	document.getElementById( ele ).style.display = 'none'; }
function open_element( ele ) { document.getElementById( ele ).style.display = 'inline';}
Toggle = function (elem,openstat) { 
	this.ele = elem; 
	this.isopen = openstat;
	this.isopen?open_element(this.ele):close_element(this.ele);
	if( Toggle.arguments.length ==3 )
		this.togglewith( Toggle.arguments[2] );
	this.toggleContentEle = '';
}
Toggle.prototype.toggle_ele = function(){ 
	this.isopen?close_element( this.ele ):open_element( this.ele ); 
	this.isopen = this.isopen?false:true;
	return;
}
Toggle.prototype.toggle_con = function () {
	if( this.toggleContentEle == '' ) return;
	tco = document.getElementById( this.toggleContentEle );
	tco.innerHTML = !this.isopen?this.toggleContentOn:this.toggleContentOff; 
}
Toggle.prototype.setToggleContent = function( id, on, off ) {
	this.toggleContentEle = id;
	this.toggleContentOn = on;
	this.toggleContentOff = off;
	
	this.toggle_con();	
}
Toggle.prototype.doTogg = function() {
		this.toggle_ele();
		this.toggle_con(); 
}
Toggle.prototype.togglewith = function(anc){
	this.toggleEle = anc;
	ee = document.getElementById(anc);
	ee.style.cursor = 'hand';
	addclick(ee,this);
}
function addclick(o,e) { o.onclick=function(){e.doTogg()}; }