jQuery(function($) {
    $(document).on('click', '#sign_up', function(){

        var html = `
            <h2>Sign up</h2>
            <form id='sign_up_form'>
                <div class="form-group">
                    <label for="user">Username</label>
                    <input type="text" class="form-control" name="user" id="user" placeholder="Enter you name"  required />
                </div>

                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" class="form-control" name="email" id="email" placeholder="Enter you email" required />
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" class="form-control" name="password" id="password" placeholder="Enter you password"  required />
                </div>

                <button type='submit' class='btn btn-primary'>Sign up</button>
            </form>
        `;

        clearResponse();
        $('#content').html(html);
    });

    $(document).on('submit', '#sign_up_form', function(){ 
        var sign_up_form=$(this);
        var form_data=JSON.stringify(sign_up_form.serializeObject());
        
        $.ajax({
            url: "api/create_user.php",
            type : "POST",
            contentType : 'application/json',
            data : form_data,
            success : function(result) {
                $('#response').html("<div class='alert alert-success'>Registration completed. Please sign in.</div>");
                sign_up_form.find('input').val('');
            },
            error: function(xhr, resp, text){
                $('#response').html("<div class='alert alert-danger'>Registration failed. Username or email already exists.</div>");
            }
        });

        return false;
    });

    $(document).on('click', '#login', function(){
        showLoginPage();
    });

    $(document).on('submit', '#login_form', function(){
        var login_form=$(this);
        var form_data=JSON.stringify(login_form.serializeObject());

        $.ajax({
            url: "api/login.php",
            type : "POST",
            contentType : 'application/json',
            data : form_data,
            success : function(result){
                setCookie("jwt", result.jwt, 1);
                showHomePage(true);
                $('#response').html("<div class='alert alert-success'>Successful login.</div>");
            },
            error: function(xhr, resp, text){
                $('#response').html("<div class='alert alert-danger'>Login error. Email or password is not correct.</div>");
                login_form.find('input').val('');
            }
        });

        return false;
    });

$(document).on('click', '#home', function(){
    showHomePage(false);
    clearResponse();
});

$(document).on('click', '#logout', function(){
    showLoginPage();
    $('#response').html("<div class='alert alert-info'>You logged out.</div>");
});

    function clearResponse(){
        $('#response').html('');
    }
 
    function showLoginPage(){
        setCookie("jwt", "", 1);
        var html = `
            <h2>Sign in</h2>
            <form id='login_form'>
                <div class='form-group'>
                    <label for='email'>Email</label>
                    <input type='email' class='form-control' id='email' name='email' placeholder='Enter you email'>
                </div>

                <div class='form-group'>
                    <label for='password'>Password</label>
                    <input type='password' class='form-control' id='password' name='password' placeholder='Enter you password'>
                </div>
    
                <button type='submit' class='btn btn-primary'>Sign in</button>
            </form>
            `;
        $('#content').html(html);
        clearResponse();
        showLoggedOutMenu();
    }

    function setCookie(cname, cvalue, exdays) {
        var d = new Date();
        d.setTime(d.getTime() + (exdays*24*60*60*1000));
        var expires = "expires="+ d.toUTCString();
        document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
    }

    function showLoggedOutMenu(){
        $("#login, #sign_up").show();
        $("#logout").hide(); 
        $("#tbar").hide(); 
    }

function showHomePage(loginOrValidate){
    var jwt = getCookie('jwt');
    if (loginOrValidate){
        $.post("api/validate_token.php", JSON.stringify({ jwt:jwt }));
        getInfoUsers();
        showLoggedInMenu();
        toolbarAction();
    } else {
        $.post("api/validate_token.php", JSON.stringify({ jwt:jwt })).done(function(result) {
            getInfoUsers();
            showLoggedInMenu();
            toolbarAction();
        })
        .fail(function(result){
            showLoginPage();
            $('#response').html("<div class='alert alert-danger'>Access denied. Please sign in!</div>");
        });
    }
}

function getInfoUsers(){
    let table = $.ajax({
        url: "api/table.php",
        type : "POST",
        contentType : 'application/json',
        success:function(result_array){
            console.log(result_array);
            $('#content').html(`<table class='table table-hover'>`+generationHTMLTable(result_array)+`</table>`);
            let $userall = document.querySelector('#userall');
            $userall.addEventListener('click', function (event) {
                let $allCheck = document.querySelectorAll('.choose')
                for (var i = 0; i < $allCheck.length; i++) {
                    if (document.querySelector('#userall').checked==false) {
                        $allCheck[i].checked = false
                    } else {
                        $allCheck[i].checked = true
                    }
                }
            });
        } 
    });
    console.log(table);
}

function toolbarAction(){
    toolAction('block');
    toolAction('unblock');
    toolAction('delete');
}

function toolAction(action){
    let $block = document.querySelector('#'+action+'[type="button"]');
    $block.addEventListener('click', function (event) {
        let $blockElem = document.querySelectorAll('.choose');
        let strQuery = []
        for (var i = 0; i < $blockElem.length; i++) {
            if ($blockElem[i].checked==true) {
                strQuery.push($blockElem[i].id.substr(1));
            }
        }
        if (strQuery!=0) {
            deleteOrBlock(((action=='delete')?true:false),((action=='block')?true:false),strQuery);
        }
    });
}

function deleteOrBlock(del,isBlock,strQuery){
    $.post(((del)?"api/delete.php":"api/block.php"), {variable: strQuery, blockUser:((isBlock)?'1':'0')});
    showHomePage(false);
}

function generationHTMLTable(arr){
    let html = `<thead class='thead-dark' style="width:100%;">
                    <tr>
                        <th scope='col'><input type="checkbox" id="userall" value="0"/></th>
                        <th scope='col'>ID</th>
                        <th scope='col'>Name</th>
                        <th scope='col'>Email</th>
                        <th scope='col'>Created</th>
                        <th scope='col'>Last login</th>
                        <th scope='col'>Block</th>
                    </tr>
                </thead>`;

    if (Array.isArray(arr)){
        arr.forEach(function(item) {
            html +=
            `<tr>
                <td class="vcenter"><input type="checkbox" class="choose" id="`+'i'+item.id+`" value="`+item.block+`"/></td>
                <th scope="row">`+((item.id)?item.id:' ')+`</th>
                <td>`+item.user+`</td>
                <td>`+item.email+`</td>
                <td>`+item.created+`</td>
                <td>`+((item.last_login)?item.last_login:'Never')+`</td>
                <td>`+((item.block==1)?'Yes':'No')+`</td>
            </tr>`
        })
    }
    return html;
}

function getCookie(cname) {
    var name = cname + "=";
    var decodedCookie = decodeURIComponent(document.cookie);
    var ca = decodedCookie.split(';');
    for(var i = 0; i <ca.length; i++) {
        var c = ca[i];
        while (c.charAt(0) == ' '){
            c = c.substring(1);
        }
        if (c.indexOf(name) == 0) {
            return c.substring(name.length, c.length);
        }
    }
    return "";
}

function showLoggedInMenu(){ 
    $("#login, #sign_up").hide();
    $("#logout").show();
    $("#tbar").show();
}


    $.fn.serializeObject = function(){
        var o = {};
        var a = this.serializeArray();
        $.each(a, function() {
            if (o[this.name] !== undefined) {
                if (!o[this.name].push) {
                    o[this.name] = [o[this.name]];
                }
                o[this.name].push(this.value || '');
            } else {
                o[this.name] = this.value || '';
            }
        });
        return o;
    }; 
});