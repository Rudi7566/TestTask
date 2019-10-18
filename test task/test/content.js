
var cur = -1;

function contentReveal() {
    var x = document.getElementById("selectContent").value;
    
    document.getElementById(x).classList.remove("hidden");

    if(cur != -1){
        document.getElementById(cur).classList.add("hidden");
    }
    
    cur = x;
  }

  function parentSelect(){
    var x = document.getElementById("selectParent").value;

    document.getElementById('parent_id').value = x;
  }

  function parentSelectDel(){
    var x = document.getElementById("selectParentDel").value;

    document.getElementById('parent_id_delete').value = x;
  }

  function parentSelectUpd(){
    var x = document.getElementById("selectParentIdUpd").value;

    document.getElementById('parent_id_update').value = x;
  }

  function IdSelect(){
    var x = document.getElementById("idSelect").value;

    document.getElementById('id_Select').value = x;
  }

  