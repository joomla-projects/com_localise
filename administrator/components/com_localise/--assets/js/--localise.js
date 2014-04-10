var Localise = new Class({
  addKey: function(target,section,newkey,newvalue) {
    newkey=newkey.replace(/[ ]/g,"_");
    newkey=newkey.replace(/[^a-zA-Z_]/g,"");
    newkey=newkey.toUpperCase();
    if (newkey !='') {
      var node = target.parentNode.parentNode;
      var div = node.parentNode.parentNode.parentNode;
      var tr= new Element('tr');
      var del=new Element('td',{'class':'paramlist_key'});
      var key=new Element('td',{'class':'paramlist_key','width':'50%'});    
      var span=new Element('span',{'class':'editlinktip'});
      var value=new Element('td');
      var img=new Element('img',{
        'title':delete_title + '::' + delete_desc,
        'class':'hasTip',
        'src':'images/publish_x.png',
        'onclick':'javascript:localise.removeKey(this)',
        'style':'cursor: pointer;'
      });
      var label=new Element('label',{'text':newkey});
      span.adopt(label);
      key.adopt(span);
      var newclass=null;
      if (newvalue=='') {
        newclass='untranslated';
      } else {
        newclass='extra'
      }
      var input=new Element('input',{'class':newclass,'type':'text','name':'strings['+section+']['+newkey+']','value':newvalue,'size':'50','onchange':"javascript: if (this.get('value')=='') this.set('class','untranslated'); else this.set('class','extra');"});
      value.adopt(input);
      tr.adopt(key,del,value);
      del.adopt(img);
      tr.inject(node,'before');
      div.setStyle('height',(div.getHeight()+tr.getHeight())+'px');
      myLocaliseTips.attach(img);
      input.focus();
    }
  },
  removeKey: function(target) {
    if (confirm(delete_this_key)) {
      var node = target.getParent().getParent();
      var div=node.getParent().getParent().getParent();
      div.setStyle('height',(div.getHeight()-node.getHeight())+'px');
      node.dispose();
    }
  },
  removeValue: function(target) {
    if (confirm(delete_this_value)) {
      var node = target.getParent().getParent();
      node.dispose();
    }
  },
  insertValue: function(target,input,title) {
    var tr = new Element('tr');
    var td1 = new Element('td',{'style':"padding: 0;padding-bottom: 3px;",'html':input});
    var td2 = new Element('td',{'style':"padding: 0;padding-bottom: 3px;",'width':"16px",'height':"16px"});
    var img = new Element('img',{'style':"cursor:pointer;",'class':"hasTip",'title':title,'src':"images/publish_x.png",'onclick':"javascript:localise.removeValue(this);"});
    tr.adopt(td1,td2);
    td1.adopt(input);
    td2.adopt(img);
    tr.inject(target.getParent().getParent(),'before');
  },
  addPanel: function() {
    $('addpanel').setStyle('display','none');
    $('newpanel').setStyle('display','');
    $('namepanel').setStyle('display','');
  },
  deletePanel: function(target) {
    if (confirm(delete_this_section))
      target.getParent().getParent().dispose();
  },
  newPanel: function() {
    /*$('addpanel').setStyle('display','');
    $('newpanel').setStyle('display','none');
    $('namepanel').setStyle('display','none');*/
    var section=$('namepanel').get('value');
    section=section.replace(/[^0-9 \-a-zA-Z_]/g,"");
    $('namepanel').set('value','');
    if (section != '') {
      var name=section.charAt(0).toUpperCase()+section.substr(1);
      var toggler=new Element('h3',{'class':'title jpane-toggler','id':section+'-page'});
      var content = new Element('div',{'class':'jpane-slider content'});
      var table=new Element('table',{'width':'100%', 'cellspacing':'1','class':'paramlist admintable'});
      var tbody=new Element('tbody');
      var tr=new Element('tr');
      var add=new Element('td',{'class':'paramlist_key'});
      var key=new Element('td',{'style':'width:50%;','class':'paramlist_key'});
      var value=new Element('td',{'style':'width:50%;','class':'paramlist_value'});
      var img=new Element('img',{
        'onclick':"javascript:localise.addKey(this,'"+section+"',$('newkey"+section+"').value,'');$('newkey"+section+"').value=''",
        'src':'components/com_localise/assets/images/icon-16-new.png',
        'title': insert_title+'::'+insert_desc,
        'class':'hasTip',
        'style':'cursor: pointer;'
      });
      var del=new Element('img',{
        'src':'images/publish_x.png',
        'title': delete_title+'::'+delete_desc,
        'class':'hasTip',
        'style':'cursor: pointer;'
      });
      del.addEvent('click',function (event) {
        event.stop();
        if (confirm(delete_this_section))
          event.target.getParent().getParent().dispose();
      });
      span=new Element('span',{'text':name});
      toggler.adopt(del,span);
      content.adopt(table);
      table.adopt(tbody);
      tbody.adopt(tr);
      tr.adopt(key,add,value);
      add.adopt(img);
      key.adopt(new Element('input',{'type':'text','size':'50','id':'newkey'+section,'style':'text-align:right;'}));
      //value.adopt(new Element('input',{'type':'text','size':'50','id':'newvalue'+section}));
      accordion.addSection(toggler, content);
      accordion.display(content);
      var panel=new Element('div',{'class':'panel'});
      panel.inject($('strings-pane').getLast(),'before');
      panel.adopt(toggler,content);
      myLocaliseTips.attach(img);
      myLocaliseTips.attach(del);
    }
  }  
});

function submitbutton(task) {
  // Validation is currently busted
  //if (task == 'translation.cancel' || document.formvalidator.isValid($('weblink-form'))) {
  if (task == 'translation.cancel') {
    submitform(task);
  }
  // @todo Deal with the editor methods
  submitform(task);
}