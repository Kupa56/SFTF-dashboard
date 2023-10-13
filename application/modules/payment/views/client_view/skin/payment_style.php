
@media (min-width: 768px) {
.my-custom-container{
width:600px;
margin: 0 auto;
}
}

@media (min-width: 992px) {
.my-custom-container{
width:720px;
margin: 0 auto;
}
}

@media (min-width: 1200px) {
.my-custom-container{
width:900px;
margin: 0 auto;
}
}

.pack-box{
width: 360px;
margin: 2% auto;
}

.my-custom-container .payment{
padding: 20px;
padding-top: 50px;
}

.my-custom-container .payment .methods{

}





.my-custom-container .payment .methods .method{
border: 2px solid #FFFFFF;
padding: 10px;
/* padding-top: 5px; */
/* padding-bottom: 10px; */
cursor: pointer;
margin-bottom: 15px;
background-color: #FFFFFF;
box-shadow: #d1d1d1 0px 4px 24px;
}

.my-custom-container .payment .methods .method .detail{
font-size: 20px;
display: block;
}

.my-custom-container .payment .methods .method .detail .icon{
float: left;
font-size: 50px;
}

.my-custom-container .payment .methods .method .detail i{
font-size: 50px;
}

.my-custom-container .payment .methods .method .detail .checked{
float: right;
}

.my-custom-container .payment .methods .method.active .detail .checked{
    visibility: visible;
}

.my-custom-container .payment .methods .method.inactive .detail .checked{
    visibility: hidden;
}


.my-custom-container .payment .methods .method .detail p{
    display: block;
    padding-top: 5px;
    line-height: 20px;
}


.my-custom-container .payment .methods .method .detail p img{
    float: left;
    height: 59px;
    padding: 0px;
    margin-right: 12px;
    padding: 5px;
}

.my-custom-container .payment .methods .method .detail p strong{
    display: block;
    padding-bottom: 6px;
    padding-top: 6px;
}
.my-custom-container .payment .methods .method .detail p span{
    color: #606060;
    font-size: 12px;
    margin-top: 0px;
    line-height: 15px !important;
    display: block;
}


.my-custom-container .payment .methods .method.active{
    border: 2px solid <?=DASHBOARD_COLOR?>;
    border-radius: 6px;
    background-color: #FFFFFF;
}

.my-custom-container .payment .methods .method.inactive{
    border: 2px solid #FFFFFF;
    border-radius: 6px;
    background-color: #FFFFFF;
}

.my-custom-container .my-invoice{
    border-radius: 6px;
    background-color: #FFFFFF;
    padding: 20px;
    border: 1px solid #e2e0e0;
    box-shadow: #d1d1d1 0px 4px 24px;
}

.my-custom-container .my-invoice .items .item{
    padding: 5px;
}

.my-custom-container .my-invoice .items .item strong{
    float: left;
}
.my-custom-container .my-invoice .items .item b{
    float: right;
}

.my-custom-container .pay-btn a, .my-custom-container .cancel-btn a{
    display: block;
    margin-top: 10px;
    padding: 11px;
    border-radius: 6px;
}


.paypal-types{
    text-align: center;
    background-color: #FFFFFF;
    border: 3px solid #FFFFFF;
    border-radius: 6px;
    border: 2px solid #FFFFFF;
    padding: 10px;
    /* padding-top: 5px; */
    /* padding-bottom: 10px; */
    cursor: pointer;
    margin-bottom: 15px;
    background-color: #FFFFFF;
    box-shadow: #d1d1d1 0px 4px 24px;
}

.paypal-types label{
margin-bottom: 0px !important;
}