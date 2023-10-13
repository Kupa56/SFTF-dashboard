
<li class=" dropdown user user-menu">

    <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-expanded="true">
    <?php

        //prepare image data
        $userImage = $this->mUserBrowser->getData("images");

        if (is_string($userImage) && $userImage != "") {
            $userImage = json_decode($userImage, JSON_OBJECT_AS_ARRAY);
        }


        $dc = $userImage;
        if (!is_array($userImage) and $dc != "") {
            $userImage = array();
            $userImage[] = $dc;
        }


        //get image url
        $imageUrl = adminAssets("images/place-holder-160.png");

        if (!empty($userImage) && isset($userImage[0]) && is_string($userImage[0])) {

            $userImage = ImageManagerUtils::getImage($userImage[0]);

            if (!empty($userImage))
                $imageUrl = $userImage;

        }elseif(isset($userImage[0]) && is_array($userImage[0])){

            if (!empty($userImage[0]))
                $imageUrl = $userImage[0]['200_200']['url'];

        }

        ?>


        <img src="<?= $imageUrl ?>" class="user-image" alt="User Image">
        <span class="hidden-xs">
                                 <?= $this->mUserBrowser->getAdmin("name") ?>
                              </span>

    </a>

</li>

<li class=" dropdown notifications-menu ">

    <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-expanded="true">
        <i class="fa fa-sliders"></i>
    </a>

    <ul class="dropdown-menu">
        <!-- <li class="header"><? /*=Translate::sprint("Role Type","")*/ ?> : <? /*=$this->mUserBrowser->getAdmin("typeAuth")*/ ?></li>-->
        <li>
            <!-- inner menu: contains the actual data -->
            <ul class="menu">
                <li>
                    <a href="<?= admin_url("user/profile") ?>"><i class="fa fa-pencil"></i>&nbsp;&nbsp;<?= Translate::sprint("Profile", "") ?>
                    </a>
                </li>
                <li>
                    <a href="<?= site_url("user/logout") ?>"><i class="fa fa-sign-out"></i>&nbsp;&nbsp;<?= Translate::sprint("Logout", "") ?>
                    </a>
                </li>
            </ul>
        </li>

    </ul>

</li>
