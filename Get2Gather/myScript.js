function display_txt_post_btn() 
{
    var txt = document.forms["txt_frm_logd_uid"]["txt_status"].value;
    var btn = document.getElementById("txt_post_btn"); 
    if (txt != "") 
    {
        btn.style.display = "block";
    }
    else
    {
        btn.style.display = "none";
    }
}

function display_txt_post_btnS()
{
    var txts = document.forms["txt_frm_srchd_uid"]["txt_status"].value;    //forms["form_name"]["element_name"].element's value
    var btns = document.getElementById("txt_post_srchd_btn");          //Grabing the txt_post button
    if (txts != "") 
    {
        btns.style.display = "block";
    }
    else 
    {
        btns.style.display = "none";
    }
}

function display_photo_post_btn() 
{
    var txt = document.forms["photo_frm_logd_uid"]["txt_status"].value;
    var btn = document.getElementById("photo_post_btn"); 
    if (txt != "") 
    {
        btn.style.display = "block";
    }
    else
    {
        btn.style.display = "none";
    }
}

function display_photo_post_btnS()
{
    var txts = document.forms["photo_frm_srchd_uid"]["txt_status"].value;    //forms["form_name"]["element_name"].element's value
    var btns = document.getElementById("photo_post_srchd_btn");          //Grabing the txt_post button
    if (txts != "") 
    {
        btns.style.display = "block";
    }
    else 
    {
        btns.style.display = "none";
    }
}

function display_cmnt_btn() 
{
    var txt = document.forms["cmnt_frm"]["txt_comnt"].value;
    var btn = document.getElementsByClassNames("cmnt_btn").length; 
    if (txt != "") 
    {
        btn.style.display = "block";
    }
    else
    {
        btn.style.display = "none";
    }
}