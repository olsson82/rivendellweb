<?php
/*********************************************************************************************************
 *                                        RIVENDELL WEB BROADCAST                                        *
 *    A WEB SYSTEM TO USE WITH RIVENDELL RADIO AUTOMATION: HTTPS://GITHUB.COM/ELVISHARTISAN/RIVENDELL    *
 *              THIS SYSTEM IS NOT CREATED BY THE DEVELOPER OF RIVENDELL RADIO AUTOMATION.               *
 * IT'S CREATED AS AN HELP TOOL ONLINE BY ANDREAS OLSSON AFTER HE FIXED BUGS IN AN OLD SCRIPT CREATED BY *
 *             BRIAN P. MCGLYNN : HTTPS://GITHUB.COM/BPM1992/RIVENDELL/TREE/RDWEB/WEB/RDPHP              *
 *        USE THIS SYSTEM AT YOUR OWN RISK. IT DO DIRECT MODIFICATION ON THE RIVENDELL DATABASE.         *
 *                 YOU CAN NOT HOLD US RESPONISBLE IF SOMETHING HAPPENDS TO YOUR SYSTEM.                 *
 *                   THE DESIGN IS DEVELOP BY SAUGI: HTTPS://GITHUB.COM/ZURAMAI/MAZER                    *
 *                                              MIT LICENSE                                              *
 *                                   COPYRIGHT (C) 2024 ANDREAS OLSSON                                   *
 *             PERMISSION IS HEREBY GRANTED, FREE OF CHARGE, TO ANY PERSON OBTAINING A COPY              *
 *             OF THIS SOFTWARE AND ASSOCIATED DOCUMENTATION FILES (THE "SOFTWARE"), TO DEAL             *
 *             IN THE SOFTWARE WITHOUT RESTRICTION, INCLUDING WITHOUT LIMITATION THE RIGHTS              *
 *               TO USE, COPY, MODIFY, MERGE, PUBLISH, DISTRIBUTE, SUBLICENSE, AND/OR SELL               *
 *                 COPIES OF THE SOFTWARE, AND TO PERMIT PERSONS TO WHOM THE SOFTWARE IS                 *
 *                       FURNISHED TO DO SO, SUBJECT TO THE FOLLOWING CONDITIONS:                        *
 *            THE ABOVE COPYRIGHT NOTICE AND THIS PERMISSION NOTICE SHALL BE INCLUDED IN ALL             *
 *                            COPIES OR SUBSTANTIAL PORTIONS OF THE SOFTWARE.                            *
 *              THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR               *
 *               IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,                *
 *              FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE              *
 *                AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER                 *
 *             LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,             *
 *             OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE             *
 *                                               SOFTWARE.                                               *
 *********************************************************************************************************/
$serviceNames = $dbfunc->getUserService($_COOKIE['username']);
$selectedService = 0;
if (isset($_COOKIE['serviceName'])) {
    $selectedService = $_COOKIE['serviceName'];
} else {
    $selectedService = $dbfunc->setAUserservice($_COOKIE['username']);
    $expire = time() + (30 * 24 * 60 * 60);
    setcookie('serviceName', $selectedService, $expire, '/');
}
?>
<div id="sidebar">
    <div class="sidebar-wrapper active">
        <div class="sidebar-header position-relative">
            <div class="d-flex justify-content-between align-items-center">
                <div class="logo">
                    <a href="index.php"><img src="<?php echo DIR; ?>/assets/static/images/rivlogo/rdairplay-128x128.png" alt="Logo" srcset=""></a>
                </div>
                <div class="theme-toggle d-flex gap-2  align-items-center mt-2">
                    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"
                        aria-hidden="true" role="img" class="iconify iconify--system-uicons" width="20" height="20"
                        preserveAspectRatio="xMidYMid meet" viewBox="0 0 21 21">
                        <g fill="none" fill-rule="evenodd" stroke="currentColor" stroke-linecap="round"
                            stroke-linejoin="round">
                            <path
                                d="M10.5 14.5c2.219 0 4-1.763 4-3.982a4.003 4.003 0 0 0-4-4.018c-2.219 0-4 1.781-4 4c0 2.219 1.781 4 4 4zM4.136 4.136L5.55 5.55m9.9 9.9l1.414 1.414M1.5 10.5h2m14 0h2M4.135 16.863L5.55 15.45m9.899-9.9l1.414-1.415M10.5 19.5v-2m0-14v-2"
                                opacity=".3"></path>
                            <g transform="translate(-210 -1)">
                                <path d="M220.5 2.5v2m6.5.5l-1.5 1.5"></path>
                                <circle cx="220.5" cy="11.5" r="4"></circle>
                                <path d="m214 5l1.5 1.5m5 14v-2m6.5-.5l-1.5-1.5M214 18l1.5-1.5m-4-5h2m14 0h2">
                                </path>
                            </g>
                        </g>
                    </svg>
                    <div class="form-check form-switch fs-6">
                        <input class="form-check-input  me-0" type="checkbox" id="toggle-dark" style="cursor: pointer">
                        <label class="form-check-label"></label>
                    </div>
                    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"
                        aria-hidden="true" role="img" class="iconify iconify--mdi" width="20" height="20"
                        preserveAspectRatio="xMidYMid meet" viewBox="0 0 24 24">
                        <path fill="currentColor"
                            d="m17.75 4.09l-2.53 1.94l.91 3.06l-2.63-1.81l-2.63 1.81l.91-3.06l-2.53-1.94L12.44 4l1.06-3l1.06 3l3.19.09m3.5 6.91l-1.64 1.25l.59 1.98l-1.7-1.17l-1.7 1.17l.59-1.98L15.75 11l2.06-.05L18.5 9l.69 1.95l2.06.05m-2.28 4.95c.83-.08 1.72 1.1 1.19 1.85c-.32.45-.66.87-1.08 1.27C15.17 23 8.84 23 4.94 19.07c-3.91-3.9-3.91-10.24 0-14.14c.4-.4.82-.76 1.27-1.08c.75-.53 1.93.36 1.85 1.19c-.27 2.86.69 5.83 2.89 8.02a9.96 9.96 0 0 0 8.02 2.89m-1.64 2.02a12.08 12.08 0 0 1-7.8-3.47c-2.17-2.19-3.33-5-3.49-7.82c-2.81 3.14-2.7 7.96.31 10.98c3.02 3.01 7.84 3.12 10.98.31Z">
                        </path>
                    </svg>
                </div>
                <div class="sidebar-toggler  x">
                    <a href="#" class="sidebar-hide d-xl-none d-block"><i class="bi bi-x bi-middle"></i></a>
                </div>
            </div>
        </div>
        <div class="sidebar-menu">
            <ul class="menu">
                <li class="sidebar-title">
                    <?= $ml->tr('SERVICE'); ?>
                </li>

                <li class="sidebar-item  has-sub">
                    <a href="#" class='sidebar-link'>
                        <i class="bi bi-stack"></i>
                        <span>
                            <?php echo $selectedService; ?>
                        </span>
                    </a>

                    <ul class="submenu ">

                        <?php

                        $i = -1;

                        foreach ($serviceNames as $name) {

                            $i++;
                            $selected = '';

                            if ($selectedService == $i)
                                $selected = 'selected ';

                            ?>

                            <li class="submenu-item  ">
                                <a href="javascript:;" onclick="SwitchService('<?php echo $name; ?>')" class="submenu-link">
                                    <?php echo $name; ?>
                                </a>

                            </li>
                            <?php
                        } //End foreach services
                        ?>

                    </ul>


                </li>

                <li class="sidebar-title">
                    <?= $ml->tr('LANGUAGE'); ?>
                </li>

                <li class="sidebar-item  has-sub">
                    <a href="#" class='sidebar-link'>
                        <i class="bi bi-flag"></i>
                        <span>
                            <?php if (isset($_COOKIE['lang'])) {
                                echo $languagesArray[$_COOKIE['lang']]['text'];
                            } else {
                                echo $languagesArray[DEFAULTLANG]['text'];
                            } ?>
                        </span>
                    </a>

                    <ul class="submenu ">

                        <?php

                        foreach ($languagesArray as $langarr) {

                            ?>

                            <li class="submenu-item  ">
                                <a href="javascript:;" onclick="changeLanguage('<?php echo $langarr['langcode']; ?>')"
                                    class="submenu-link">
                                    <?php echo $langarr['text']; ?>
                                </a>

                            </li>
                            <?php
                        }
                        ?>

                    </ul>


                </li>

                <li class="sidebar-title">
                    <?= $ml->tr('MENU'); ?>
                </li>

                <li class="sidebar-item <?php if ($pagecode == 'dash') { ?>active<?php } ?>">
                    <a href="<?php echo DIR; ?>/dash" class='sidebar-link'>
                        <i class="bi bi-grid-fill"></i>
                        <span>
                            <?= $ml->tr('DASHBOARD'); ?>
                        </span>
                    </a>


                </li>
                <li class="sidebar-item <?php if ($pagecode == 'library') { ?>active<?php } ?>">
                    <a href="<?php echo DIR; ?>/library/carts" class='sidebar-link'>
                        <i class="bi bi-collection-play-fill"></i>
                        <span>
                            <?= $ml->tr('LIBRARY'); ?>
                        </span>
                    </a>


                </li>
                <li class="sidebar-item <?php if ($pagecode == 'logs') { ?>active<?php } ?>">
                    <a href="<?php echo DIR; ?>/logedit/logs" class='sidebar-link'>
                        <i class="bi bi-receipt"></i>
                        <span>
                            <?= $ml->tr('LOGS'); ?>
                        </span>
                    </a>


                </li>
                <?php if ($json_sett["usrsett"][$_COOKIE['username']]["rdcatch"] == 1) { ?>
                    <li class="sidebar-item <?php if ($pagecode == 'rdcatch') { ?>active<?php } ?>">
                    <a href="<?php echo DIR; ?>/rdcatch" class='sidebar-link'>
                        <i class="bi bi-robot"></i>
                        <span>
                            <?= $ml->tr('RDCATCH'); ?>
                        </span>
                    </a>


                </li>

              <?php  } ?>

                <?php if ($info->checkusrRights('MODIFY_TEMPLATE_PRIV')) { ?>
                <li class="sidebar-title">
                    <?= $ml->tr('LOGMANAGER'); ?>
                </li>
                <li class="sidebar-item <?php if ($pagecode == 'events') { ?>active<?php } ?>">
                    <a href="<?php echo DIR; ?>/manager/events" class='sidebar-link'>
                        <i class="bi bi-calendar-event"></i>
                        <span>
                            <?= $ml->tr('EVENTS'); ?>
                        </span>
                    </a>


                </li>
                <li class="sidebar-item <?php if ($pagecode == 'clocks') { ?>active<?php } ?>">
                    <a href="<?php echo DIR; ?>/manager/clocks" class='sidebar-link'>
                        <i class="bi bi-clock"></i>
                        <span>
                            <?= $ml->tr('CLOCKS'); ?>
                        </span>
                    </a>


                </li>
                <li class="sidebar-item <?php if ($pagecode == 'grids') { ?>active<?php } ?>">
                    <a href="<?php echo DIR; ?>/manager/grids" class='sidebar-link'>
                        <i class="bi bi-ui-checks-grid"></i>
                        <span>
                            <?= $ml->tr('GRIDS'); ?>
                        </span>
                    </a>
                </li>
                <?php } ?>
                <?php if (isset($json_sett["admin"][$_COOKIE['username']]["username"])) { ?>
                    <li class="sidebar-item <?php if ($pagecode == 'admindash' || $pagecode == 'settings' || $pagecode == 'users' || $pagecode == 'rdairplay' || $pagecode == 'rdpanel' || $pagecode == 'groups'|| $pagecode == 'schedcodes' || $pagecode == 'hosts' || $pagecode == 'services') { ?>active<?php } ?> has-sub">
                        <a href="#" class='sidebar-link'>
                            <i class="bi bi-gear"></i>
                            <span>
                                <?= $ml->tr('ADMIN'); ?>
                            </span>
                        </a>

                        <ul
                            class="submenu <?php if ($pagecode == 'admindash' || $pagecode == 'settings' || $pagecode == 'users' || $pagecode == 'rdairplay' || $pagecode == 'rdpanel' || $pagecode == 'groups'|| $pagecode == 'schedcodes' || $pagecode == 'hosts' || $pagecode == 'services') { ?>active<?php } ?>">


                            <li class="submenu-item <?php if ($pagecode == 'admindash') { ?>active<?php } ?>">
                                <a href="<?php echo DIR; ?>/admin/dash" class="submenu-link">
                                    <?= $ml->tr('ADMINDASH'); ?>
                                </a>
                            </li>
                            <?php if ($json_sett["admin"][$_COOKIE['username']]["settings"] == 1) { ?>
                                <li class="submenu-item <?php if ($pagecode == 'settings') { ?>active<?php } ?>">
                                    <a href="<?php echo DIR; ?>/admin/settings" class="submenu-link">
                                        <?= $ml->tr('SETTINGS'); ?>
                                    </a>
                                </li>
                            <?php } ?>
                            <?php if ($json_sett["admin"][$_COOKIE['username']]["users"] == 1) { ?>
                                <li class="submenu-item <?php if ($pagecode == 'users') { ?>active<?php } ?>">
                                    <a href="<?php echo DIR; ?>/admin/users" class="submenu-link">
                                        <?= $ml->tr('USERS'); ?>
                                    </a>
                                </li>
                            <?php } ?>
                            <?php if ($json_sett["admin"][$_COOKIE['username']]["groups"] == 1) { ?>
                                <li class="submenu-item <?php if ($pagecode == 'groups') { ?>active<?php } ?>">
                                    <a href="<?php echo DIR; ?>/admin/groups" class="submenu-link">
                                        <?= $ml->tr('GROUPS'); ?>
                                    </a>
                                </li>
                            <?php } ?>
                            <?php if ($json_sett["admin"][$_COOKIE['username']]["sched"] == 1) { ?>
                                <li class="submenu-item <?php if ($pagecode == 'schedcodes') { ?>active<?php } ?>">
                                    <a href="<?php echo DIR; ?>/admin/schedcodes" class="submenu-link">
                                        <?= $ml->tr('SCHEDULERCODES'); ?>
                                    </a>
                                </li>
                            <?php } ?>
                            <?php if ($json_sett["admin"][$_COOKIE['username']]["services"] == 1) { ?>
                                <li class="submenu-item <?php if ($pagecode == 'services') { ?>active<?php } ?>">
                                    <a href="<?php echo DIR; ?>/admin/services" class="submenu-link">
                                        <?= $ml->tr('SERVICES'); ?>
                                    </a>
                                </li>
                            <?php } ?>
                            <?php if ($json_sett["admin"][$_COOKIE['username']]["hosts"] == 1) { ?>
                                <li class="submenu-item <?php if ($pagecode == 'hosts') { ?>active<?php } ?>">
                                    <a href="<?php echo DIR; ?>/admin/hosts" class="submenu-link">
                                        <?= $ml->tr('RIVHOSTS'); ?>
                                    </a>
                                </li>
                                <li class="submenu-item <?php if ($pagecode == 'rdairplay') { ?>active<?php } ?>">
                                    <a href="<?php echo DIR; ?>/admin/hosts/rdairplay" class="submenu-link">
                                        <?= $ml->tr('RDAIRPLAY'); ?>
                                    </a>
                                </li>
                                <li class="submenu-item <?php if ($pagecode == 'rdpanel') { ?>active<?php } ?>">
                                    <a href="<?php echo DIR; ?>/admin/hosts/rdpanel" class="submenu-link">
                                        <?= $ml->tr('RDPANEL'); ?>
                                    </a>
                                </li>
                            <?php } ?>
                        </ul>
                    </li>

                <?php } ?>
                <li class="sidebar-title">
                    <?= $ml->tr('OTHER'); ?>
                </li>
                <li class="sidebar-item <?php if ($pagecode == 'usersett') { ?>active<?php } ?>">
                    <a href="<?php echo DIR; ?>/usrsett" class='sidebar-link'>
                        <i class="bi bi-person-gear"></i>
                        <span>
                            <?= $ml->tr('ACCINFO'); ?>
                        </span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a href="https://olsson82.github.io/rivwebdoc/" target="_blank" class='sidebar-link'>
                        <i class="bi bi-question-circle"></i>
                        <span>
                            <?= $ml->tr('DOCUMENTATION'); ?>
                        </span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a href="<?php echo DIR; ?>/logout" class='sidebar-link'>
                        <i class="bi bi-box-arrow-right"></i>
                        <span>
                            <?= $ml->tr('LOGOUT'); ?>
                        </span>
                    </a>
                </li>

            </ul>
        </div>
    </div>
</div>