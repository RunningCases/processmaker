<html>
    <head>
        <?php
            $oHeadPublisher = & headPublisher::getSingleton();
            echo htmlentities($oHeadPublisher->getExtJsStylesheets(SYS_SKIN));
        ?>
        <style>
            html, body, iframe {
                border:none;
                width: 100%;
                top:0px;
                height:100%;
                margin: 0px;
                padding: 0px;
            }
            iframe {
                position: absolute;
                border:none;
                width: 100%;
                top:60px;
                bottom:0px;
                margin: 0px;
                padding: 0px;
            }
            .header {
                height: 60px;
            }
            #avatar {
                background-color: buttonface;
                width: 48px;
                height: 48px;
                border-radius: 50%;
                border: 1px solid black;
                margin-left: 8px;
                margin-top: 4px;
                display: inline-block;
                position: absolute;
            }
            #userInformation {
                display: inline-block;
                margin-top: 20px;
                position: absolute;
                margin-left: 64px;
            }
            #logout {
                margin-top: 20px;
                position: absolute;
                margin-left: 64px;
                right: 8px;
            }
        </style>
    </head>
    <body>
        <?php
        $conf = new Configurations();
        $configuration = $conf->getConfiguration("ENVIRONMENT_SETTINGS", "", "", "", "", $outResult);
        ?>
        <div class="header">
            <img id="avatar">
            <span id="userInformation"></span>
            <a href="javascript:logout(1)" id="logout"><?=G::LoadTranslation('ID_LOGOUT')?></a>
        </div>
        <script>
            var app = {
                $element:{
                    avatar: document.getElementById("avatar"),
                    userInformation: document.getElementById("userInformation")
                },
                setAvatar:function(src){
                    this.$element.avatar.src=src;
                },
                getAvatar:function(){
                    return this.$avatar.src;
                },
                setUserInformation:function(textContent){
                    this.$element.userInformation.textContent=textContent;
                },
                getUserInformation:function(){
                    return this.$element.userInformation.textContent;
                },
                loadUserInformation:function(userInformation) {
                    var format = <?= G::json_encode($configuration['format']) ?>;
                    this.setAvatar(userInformation.image);
                    for(var key in userInformation) {
                        format = format.replace("@"+key, userInformation[key]);
                    };
                    this.setUserInformation(format);
                }
            };
            function logout(reload) {
                $.ajax({
                    url: '../login/login',
                    success: function () {
                        if (reload) {
                            localStorage.removeItem('weData');
                            location.reload();
                        }
                    }
                });
            }
        </script>
        <iframe id="iframe" ></iframe>
        <script src="/lib/js/jquery-1.10.2.min.js"></script>
        <script>
            !function () {
            <?php

            $webEntryModel = \WebEntryPeer::retrieveByPK($weUid);
            ?>
                var processUid = <?= G::json_encode($webEntryModel->getProUid()) ?>;
                var tasUid = <?= G::json_encode($webEntryModel->getTasUid()) ?>;
                var weUid = <?= G::json_encode($webEntryModel->getWeUid()) ?>;
                var forceLogin = <?= G::json_encode($webEntryModel->getWeAuthentication()==='LOGIN_REQUIRED') ?>;
                var isLogged = <?= G::json_encode(!empty($_SESSION['USER_LOGGED'])) ?>;
                var closeSession = <?= G::json_encode($webEntryModel->getWeCallback()==='CUSTOM_CLEAR') ?>;
                var onLoadIframe = function () {};
                var initialWeData = localStorage.weData;
                var weData = {};
                if (localStorage.weData) {
                    try {
                        weData = JSON.parse(localStorage.weData);
                        if (weData.TAS_UID!=tasUid) {
                            //TAS_UID is different, reset.
                            localStorage.removeItem('weData');
                        }
                    } catch (e) {
                        //corrupt weData, reset.
                        localStorage.removeItem('weData');
                    }
                }
                $("#iframe").load(function (event) {
                    onLoadIframe(event);
                });
                var getContentDocument = function (iframe) {
                    return (iframe.contentDocument) ?
                      iframe.contentDocument :
                      iframe.contentWindow.document;
                }
                var open = function (url, callback) {
                    return new Promise(function (resolve, reject) {
                        var iframe = document.getElementById("iframe");
                        if (typeof callback === 'function') {
                            iframe.style.opacity = 0;
                            onLoadIframe = (function () {
                                return function (event) {
                                    if (callback(event, resolve, reject)) {
                                        iframe.style.opacity = 1;
                                    }
                                }
                            })();
                        } else {
                            iframe.style.opacity = 1;
                            onLoadIframe = function () {};
                        }
                        iframe.src = url;
                        window.fullfill = function () {
                            resolve.apply(this, arguments);
                        };
                        window.reject = function () {
                            reject(this, arguments);
                        };
                    });
                }
                var verifyLogin = function () {
                    if (forceLogin) {
                        return login();
                    } else {
                        return anonymousLogin();
                    }
                }
                var login = function () {
                    return new Promise(function (logged, failure) {
                        if (!isLogged) {
                            console.log("login");
                            open('../login/login?u=' + encodeURIComponent(location.pathname + '/../../webentry/logged'))
                              .then(function (userInformation) {
                                  logged(userInformation);
                              })
                              .catch(function () {
                                  failure();
                              });
                        } else {
                            logged();
                        }
                    });
                }
                var anonymousLogin = function () {
                    return new Promise(function (resolve, failure) {
                        console.log("anonymousLogin");
                        $.ajax({
                            url: '../services/webentry/anonymous_login',
                            method: 'get',
                            dataType: 'json',
                            data: {
                                we_uid: weUid,
                            },
                            success: function (userInformation) {
                                resolve(userInformation);
                            },
                            error: function (data) {
                                failure(data);
                            }
                        });
                    });
                }
                var initCase = function (userInformation) {
                    return new Promise(function (resolve, reject) {
                        console.log("userInformation:", userInformation);
                        app.loadUserInformation(userInformation);
                        if (!localStorage.weData) {
                            console.log("initCase");
                            $.ajax({
                                url: '../cases/casesStartPage_Ajax',
                                method: 'post',
                                dataType: 'json',
                                data: {
                                    action: 'startCase',
                                    processId: processUid,
                                    taskId: tasUid,
                                },
                                success: function (data) {
                                    data.TAS_UID = tasUid;
                                    localStorage.weData = JSON.stringify(data);
                                    resolve(data);
                                }
                            });
                        } else {
                            console.log("openCase");
                            resolve(JSON.parse(localStorage.weData));
                        }
                    });
                }
                var casesStep = function (data) {
                    return new Promise(function (resolve, reject) {
                        console.log("casesStep");
                        open(
                            '../cases/cases_Open?APP_UID=' + encodeURIComponent(data.APPLICATION) +
                            '&DEL_INDEX=' + encodeURIComponent(data.INDEX) +
                            '&action=draft',
                            function (event, resolve, reject) {
                                var contentDocument = getContentDocument(event.target);
                                var stepTitle = contentDocument.getElementsByTagName("title");
                                if (!stepTitle || stepTitle[0].textContent === 'Runtime Exception.') {
                                    if (contentDocument.location.search.match(/&POSITION=10000&/)) {
                                        //Catch error if webentry was deleted.
                                        reject();
                                        return false;
                                    }
                                }
                                return true;
                            }
                        ).then(function (callbackUrl) {
                              resolve(callbackUrl);
                        })
                        .catch(function () {
                            reject();
                        });
                    });
                }
                var routeWebEntry = function (callbackUrl) {
                    return new Promise(function (resolve, reject) {
                        console.log("routeWebEntry", callbackUrl);
                        resolve(callbackUrl);
                    });
                }
                var closeWebEntry = function (callbackUrl) {
                    return new Promise(function (resolve, reject) {
                        console.log("closeWebEntry");
                        localStorage.removeItem("weData");
                        if (closeSession) {
                            logout(false);
                        }
                        resolve(callbackUrl);
                    });
                }
                var redirectCallback = function (callbackUrl) {
                    return new Promise(function (resolve, reject) {
                        console.log("redirect");
                        //location.href = callbackUrl;
                        open(callbackUrl);
                        resolve();
                    });
                }
                //Errors
                var errorLogin = function () {
                    return new Promise(function (resolve, reject) {
                        console.log("error0");
                    });
                }
                var errorInitCase = function () {
                    return new Promise(function (resolve, reject) {
                        console.log("error");
                    });
                }
                var errorStep = function () {
                    return new Promise(function (resolve, reject) {
                        console.log("Step Error");
                        if (initialWeData !== undefined) {
                            //Try to reset the localStorage WebEntry data and restart flow
                            localStorage.removeItem("weData");
                            location.reload();
                        } else {

                        }
                    });
                }
                var errorRouting = function () {
                    return new Promise(function (resolve, reject) {
                        console.log("error");
                    });
                }
                //Execute WebEntry Flow
                verifyLogin().catch(errorLogin)
                  .then(initCase).catch(errorInitCase)
                  .then(casesStep).catch(errorStep)
                  .then(routeWebEntry).catch(errorRouting)
                  .then(closeWebEntry)
                  .then(redirectCallback);
            }();
        </script>
    </body>
</html>