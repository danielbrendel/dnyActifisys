/*
    Actifisys (dnyActifisys) developed by Daniel Brendel

    (C) 2019 - 2022 by Daniel Brendel

    Version: 1.0
    Contact: dbrendel1988<at>gmail<dot>com
    GitHub: https://github.com/danielbrendel/

    Released under the MIT license
*/

require('./bootstrap');

const MAX_ACTIVITY_USERNAME_LENGTH = 35;
const MAX_ACTIVITY_TITLE_LENGTH = 40;
const MAX_ACTIVITY_DESCRIPTION_LENGTH = 130;

window.vue = new Vue({
    el: '#app',

    data: {
        bShowRecover: false,
        bShowRegister: false,
        bShowLogin: false,
        bShowCreateActivity: false,
        bShowReplyThread: false,
        bShowActivityCanceled: false,
        bShowEditComment: false,
        bShowCreateFaq: false,
        bShowEditFaq: false,
        bShowCreateTheme: false,
        bShowEditTheme: false,
        bShowEditActivity: false,
        bShowCancelActivity: false,
        bShowActivityExpired: false,
        bShowUploadImage: false,
        bShowCreateCategory: false,
        bShowEditCategory: false,
        bShowAddLocation: false,
        bShowEditLocation: false,
        bShowPurchaseProMode: false,
        bShowCreateThread: false,
        bShowReplyForumThread: false,
        bShowEditForumThread: false,
        bShowEditForumPost: false,
        bShowCreateForum: false,
        bShowEditForum: false,
        bShowEditMarketAdvert: false,
        bShowLinkFilter: false,
        bShowGalleryUpload: false,

        app_project: 'Actifisys',

        lang: {
            copiedToClipboard: 'Text has been copied to clipboard!',
            edit: 'Edit',
            lock: 'Lock',
            expandThread: 'Expand thread',
            reply: 'Reply',
            report: 'Report',
            ignore: 'Ignore',
            view: 'View',
            remove: 'Remove',
            verifiedUser: 'Verified user',
            confirmLockForumPost: 'Do you want to lock this forum post?',
            forumPostEdited: 'Edited',
            share_whatsapp: 'Share with WhatsApp',
            share_twitter: 'Share with Twitter',
            share_facebook: 'Share with Facebook',
            share_sms: 'Share by SMS',
            share_email: 'Share by E-Mail',
            share_clipboard: 'Copy to Clipboard',
            marketplace_advert_by: 'By :name',
            linkfilter_title: 'Visit :url',
            linkfilter_hint: 'You are about to visit :url. :project is not responsible for its content. Do you want to proceed?',
            gallery_item_by: 'By :name',
            noTagsSpecified: 'No tags specified'
        }
    },

    methods: {
        handleCookieConsent: function () {
            //Show cookie consent if not already for this client

            let cookies = document.cookie.split(';');
            let foundCookie = false;
            for (let i = 0; i < cookies.length; i++) {
                if (cookies[i].indexOf('cookieconsent') !== -1) {
                    foundCookie = true;
                    break;
                }
            }

            if (foundCookie === false) {
                document.getElementById('cookie-consent').style.display = 'inline-block';
            }
        },

        clickedCookieConsentButton: function () {
            let expDate = new Date(Date.now() + 1000 * 60 * 60 * 24 * 365);
            document.cookie = 'cookieconsent=1; expires=' + expDate.toUTCString() + ';';

            document.getElementById('cookie-consent').style.display = 'none';
        },

        setLocationCookieValue: function(city) {
            let expDate = new Date(Date.now() + 1000 * 60 * 60 * 24 * 365);
            document.cookie = 'filter_location=' + ((city.length > 0) ? city : '_all') + '; expires=' + expDate.toUTCString() + '; path=/;';
        },

        setDateCookieValue: function(from, till) {
            let expDate = new Date(Date.now() + 1000 * 60 * 60 * 24 * 365);

            document.cookie = 'filter_date_from=' + (((typeof from.value !== 'undefined') && (from.value.length > 0)) ? from.value : '_default') + '; expires=' + expDate.toUTCString() + '; path=/;';
            document.cookie = 'filter_date_till=' + (((typeof till.value !== 'undefined') && (till.value.length > 0)) ? till.value : '_default') + '; expires=' + expDate.toUTCString() + '; path=/;';
        },

        getLocationCookieValue: function() {
            let cookies = document.cookie.split(';');
            for (let i = 0; i < cookies.length; i++) {
                if (cookies[i].indexOf('filter_location') !== -1) {
                    return cookies[i].substr(cookies[i].indexOf('=') + 1);
                }
            }

            return '_all';
        },

        getDateFromCookieValue: function() {
            let cookies = document.cookie.split(';');
            for (let i = 0; i < cookies.length; i++) {
                if (cookies[i].indexOf('filter_date_from') !== -1) {
                    let cookieDate = cookies[i].substr(cookies[i].indexOf('=') + 1);
                    
                    let dtCookie = Date.parse(cookieDate);
                    let dtNow = new Date();

                    if (dtNow >= dtCookie) {
                        return dtNow.getFullYear() + "-" + ((dtNow.getMonth() + 1 <= 9) ? '0' + (dtNow.getMonth() + 1) : dtNow.getMonth() + 1) + "-" + dtNow.getDate();
                    }

                    return cookieDate;
                }
            }

            return '_default';
        },

        clearFilterCookies: function() {
            this.setLocationCookieValue('_all');
            this.setDateCookieValue('_default', '_default');
        },

        getDateTillCookieValue: function() {
            let cookies = document.cookie.split(';');
            for (let i = 0; i < cookies.length; i++) {
                if (cookies[i].indexOf('filter_date_till') !== -1) {
                    let cookieDate = cookies[i].substr(cookies[i].indexOf('=') + 1);

                    let dtCookie = Date.parse(cookieDate);
                    let dtNow = new Date();

                    if (dtNow >= dtCookie) {
                        return dtNow.getFullYear() + "-" + ((dtNow.getMonth() + 1 <= 9) ? '0' + (dtNow.getMonth() + 1) : dtNow.getMonth() + 1) + "-" + dtNow.getDate();
                    }

                    return cookieDate;
                }
            }

            return '_default';
        },

        ajaxRequest: function (method, url, data = {}, successfunc = function(data){}, finalfunc = function(){}, config = {})
        {
            //Perform ajax request

            let func = window.axios.get;
            if (method == 'post') {
                func = window.axios.post;
            } else if (method == 'patch') {
                func = window.axios.patch;
            } else if (method == 'delete') {
                func = window.axios.delete;
            }

            func(url, data, config)
                .then(function(response){
                    successfunc(response.data);
                })
                .catch(function (error) {
                    console.log(error);
                })
                .finally(function(){
                        finalfunc();
                    }
                );
        },

        showError: function ()
        {
            document.getElementById('flash-error').style.display = 'inherit';
            setTimeout(function() { document.getElementById('flash-error').style.display = 'none'; }, 3500);
        },

        showSuccess: function()
        {
            document.getElementById('flash-success').style.display = 'inherit';
            setTimeout(function() { document.getElementById('flash-success').style.display = 'none'; }, 3500);
        },

        invalidLoginEmail: function () {
            let el = document.getElementById("loginemail");

            if ((el.value.length == 0) || (el.value.indexOf('@') == -1) || (el.value.indexOf('.') == -1)) {
                el.classList.add('is-danger');
            } else {
                el.classList.remove('is-danger');
            }
        },

        invalidRecoverEmail: function () {
            let el = document.getElementById("recoveremail");

            if ((el.value.length == 0) || (el.value.indexOf('@') == -1) || (el.value.indexOf('.') == -1)) {
                el.classList.add('is-danger');
            } else {
                el.classList.remove('is-danger');
            }
        },

        invalidLoginPassword: function () {
            let el = document.getElementById("loginpw");

            if (el.value.length == 0) {
                el.classList.add('is-danger');
            } else {
                el.classList.remove('is-danger');
            }
        },

        invalidRequiredInput: function(obj, btn) {
            if (obj.value.length === 0) {
                obj.classList.add('is-danger');
                btn.disabled = true;
            } else {
                obj.classList.remove('is-danger');
                btn.disabled = false;
            }
        },

        invalidDate: function(obj, hint, btn) {
            let dateVal = new Date(obj.value);
            let curDate = new Date();
            if (dateVal.setHours(0, 0, 0, 0) < curDate.setHours(0, 0, 0, 0)) {
                hint.classList.remove('is-hidden');
                btn.disabled = true;
            } else {
                hint.classList.add('is-hidden');
                btn.disabled = false;
            }
        },

        invalidCreateActivity: function() {
            let btn = document.getElementById('btnCreateActivity');

            this.invalidRequiredInput(document.getElementById('caTitle'), btn);
            this.invalidRequiredInput(document.getElementById('caDescription'), btn);
            this.invalidDate(document.getElementById('caDateFrom'), document.getElementById('activity-date-hint'), btn);
            //this.invalidRequiredInput(document.getElementById('caLocation'), btn);
        },

        toggleActivityOptions: function(elem) {
            if (elem.classList.contains('is-active')) {
                elem.classList.remove('is-active');
            } else {
                elem.classList.add('is-active');
            }
        },

        toggleContextMenu: function(elem) {
            if (elem.classList.contains('is-active')) {
                elem.classList.remove('is-active');
            } else {
                elem.classList.add('is-active');
            }
        },

        toggleCommentOptions: function(elem) {
            if (elem.classList.contains('is-active')) {
                elem.classList.remove('is-active');
            } else {
                elem.classList.add('is-active');
            }
        },

        toggleActivityTags: function(elem) {
            if (elem.classList.contains('is-active')) {
                elem.classList.remove('is-active');
            } else {
                elem.classList.add('is-active');
            }
        },

        copyToClipboard: function(text) {
            const el = document.createElement('textarea');
            el.value = text;
            document.body.appendChild(el);
            el.select();
            document.execCommand('copy');
            document.body.removeChild(el);
            alert(this.lang.copiedToClipboard);
        },

        renderThread: function(elem, adminOrOwner = false, isSubComment = false, parentId = 0, isAuth = false) {
            let options = '';

            if (adminOrOwner) {
                options = `
            <a onclick="window.vue.showEditComment(` + elem.id + `); window.vue.toggleCommentOptions(document.getElementById('thread-options-` + elem.id + `'));" href="javascript:void(0)" class="dropdown-item">
                <i class="far fa-edit"></i>&nbsp;` + this.lang.edit + `
            </a>
            <a onclick="window.vue.lockComment(` + elem.id + `); window.vue.toggleCommentOptions(document.getElementById('thread-options-` + elem.id + `'));" href="javascript:void(0)" class="dropdown-item">
                <i class="fas fa-times"></i>&nbsp;` + this.lang.lock + `
            </a>
            <hr class="dropdown-divider">
        `;
            }

            if (isAuth) {
                options += `
                <a href="javascript:void(0)" onclick="window.vue.reportComment(` + elem.id + `); window.vue.toggleCommentOptions(document.getElementById('thread-options-` + elem.id + `'));" class="dropdown-item">
                    ` + this.lang.report + `
                </a>
                `;
            }

            let threadOptions = '';
            if (options.length > 0) {
                threadOptions = `
                    <div class="thread-header-options is-inline-block">
                        <div class="dropdown is-right" id="thread-options-` + elem.id + `">
                            <div class="dropdown-trigger" onclick="window.vue.toggleCommentOptions(document.getElementById('thread-options-` + elem.id + `'));">
                                <i class="fas fa-ellipsis-v is-pointer"></i>
                            </div>
                            <div class="dropdown-menu" role="menu">
                                <div class="dropdown-content">
                                    ` + options + `
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            }

            let expandThread = '';
            if (elem.subCount > 0) {
                expandThread = `<div class="thread-footer-subthread is-inline-block is-centered"><a class="is-color-grey" href="javascript:void(0)" onclick="window.vue.fetchSubThreadPosts(` + elem.id + `)">` + this.lang.expandThread + `</a></div>`;
            }

            let replyThread = '';
            if (isAuth) {
                replyThread = `<div class="is-inline-block float-right"><a class="is-color-grey" href="javascript:void(0)" onclick="document.getElementById('thread-reply-parent').value = '` + ((isSubComment) ? parentId : elem.id) + `'; document.getElementById('thread-reply-textarea').value = '` + elem.user.name + `: '; window.vue.bShowReplyThread = true;">` + this.lang.reply + `</a></div>`;
            }

            let html = `
                <div id="thread-` + elem.id + `" class="thread-elem ` + ((isSubComment) ? 'is-sub-comment': '') + `">
                    <a name="` + elem.id + `"></a>

                    <div class="thread-header">
                        <div class="thread-header-avatar is-inline-block">
                            <img width="24" height="24" src="` + window.location.origin + `/gfx/avatars/` + elem.user.avatar + `" class="is-pointer" onclick="location.href = '` + window.location.origin + `/user/` + elem.user.slug + `';" title="` + elem.user.name + `">
                        </div>

                        <div class="thread-header-info is-inline-block">
                            <div><a href="` + window.location.origin + `/user/` + elem.user.slug + `" class="is-color-grey">` + elem.user.name + `</a>` + ((elem.user.verified) ? '&nbsp;<i class="far fa-check-circle" title="' + this.lang.verifiedUser + '"></i>' : '') +  `</div>
                            <div title="` + elem.created_at + `">` + elem.diffForHumans + `</div>
                        </div>

                        ` + threadOptions + `
                    </div>

                    <div class="thread-text is-wordbreak" id="thread-text-` + elem.id + `">` + elem.text + `</div>

                    <div class="thread-footer">
                        ` + expandThread + `
                        ` + replyThread + `
                    </div>

                    <div id="sub-thread-` + elem.id + `"></div>
                </div>
            `;

            return html;
        },

        renderActivity: function(elem) {
            let headerStyle = ``;
            let headerOverlay = ``;
            if (elem.category !== 0) {
                headerStyle = `style="background-image: url('` + window.location.origin + '/gfx/categories/' + elem.categoryData.image + `'); background-size: cover; background-repeat: no-repeat;"`;
                headerOverlay = `class="activity-header-overlay"`
            }

            let userOptions = '';
            if ((window.user !== null) && (typeof window.user.id !== 'undefined') && (window.user.id !== elem.user.id)) {
                userOptions = `
                    <hr class="dropdown-divider">

                    <a class="dropdown-item is-color-black" href="` + window.location.origin + '/activity/' + elem.id + '/report' + `">
                        ` + this.lang.report + `
                    </a>

                    <a class="dropdown-item is-color-black" href="` + window.location.origin + '/user/' + elem.user.id + '/ignore/add' + `">
                        ` + this.lang.ignore + `
                    </a>
                `;
            }

            let taglist = '';
            let tags = elem.tags.split(' ');
            for (let i = 0; i < tags.length; i++) {
                if (tags[i].length > 0) {
                    taglist += `
                        <a href="` + window.location.origin + '?tag=' + tags[i] + `" class="dropdown-item">
                            #` + tags[i] + `
                        </a>
                    `;
                }
            }
            let tagcode = '';
            if (taglist.length > 0) {
                tagcode = `
                    <div class="activty-dropdown-tags dropdown is-right is-inline-block" id="activity-tags-` + elem.id + `">
                        <div class="dropdown-menu is-color-black" role="menu">
                            <div class="dropdown-content">
                                ` + taglist + `
                            </div>
                        </div>
                    </div>
                `;
            }

            let html = '';

            let dtFrom = new Date(elem.date_of_activity_from);
            let dtStrFrom = dtFrom.getFullYear() + "-" + dtFrom.getMonth() + "-" + dtFrom.getDay();
            let dtTill = new Date(elem.date_of_activity_till);
            let dtStrTill = dtTill.getFullYear() + "-" + dtTill.getMonth() + "-" + dtTill.getDay();
            
            let sameDate = dtStrFrom == dtStrTill;

            if (elem._type === 'activity') {
                html = `<div class="activity ` + ((elem.running) ? 'activity-is-running' : '') + `">
                <div class="activity-header" ` + headerStyle + `>
                    <div ` + headerOverlay + `>
                        <div class="activity-user">
                            <center><div class="activity-user-avatar"><img src="` + window.location.origin + '/gfx/avatars/' + elem.user.avatar + `" class="is-pointer" onclick="location.href = '` + window.location.origin + '/user/' + elem.user.id + `';"></div>
                                <div class="activity-user-name"><a href="` + window.location.origin + '/user/' + elem.user.slug + `">` + ((elem.user.name.length > MAX_ACTIVITY_USERNAME_LENGTH) ? elem.user.name.substr(0, MAX_ACTIVITY_USERNAME_LENGTH) + '...': elem.user.name) + `</a>` + ((elem.user.verified) ? '&nbsp;<i class="far fa-check-circle" title="' + this.lang.verifiedUser + '"></i>' : '') + `</div></center>
                        </div>

                        <div class="activity-qo">
                            <div class="dropdown is-right" id="activity-qo-` + elem.id + `">
                                <div class="dropdown-trigger">
                                    <i class="fas fa-ellipsis-v is-pointer" onclick="window.vue.toggleActivityOptions(document.getElementById('activity-qo-` + elem.id + `'));"></i>
                                </div>
                                <div class="dropdown-menu" role="menu">
                                    <div class="dropdown-content">
                                        <a onclick="window.vue.toggleActivityOptions(document.getElementById('activity-qo-` + elem.id + `'));" href="whatsapp://send?text=` + window.location.origin + '/activity/' + elem.id + ` - ` + elem.title + `" class="dropdown-item is-color-black">
                                            <i class="fab fa-whatsapp"></i>&nbsp;` + window.vue.lang.share_whatsapp + `
                                        </a>
                                        <a onclick="window.vue.toggleActivityOptions(document.getElementById('activity-qo-` + elem.id + `'));" href="https://twitter.com/share?url=` + encodeURI(window.location.origin + '/activity/' + elem.id) + `&text=` + elem.title + `" class="dropdown-item is-color-black">
                                            <i class="fab fa-twitter"></i>&nbsp;` + window.vue.lang.share_twitter + `
                                        </a>
                                        <a onclick="window.vue.toggleActivityOptions(document.getElementById('activity-qo-` + elem.id + `'));" href="https://www.facebook.com/sharer/sharer.php?u=` + window.location.origin + '/activity/' + elem.id + `" class="dropdown-item is-color-black">
                                            <i class="fab fa-facebook"></i>&nbsp;` + window.vue.lang.share_facebook + `
                                        </a>
                                        <a onclick="window.vue.toggleActivityOptions(document.getElementById('activity-qo-` + elem.id + `'));" href="mailto:name@domain.com?body=` + window.location.origin + '/activity/' + elem.id + ` - ` + elem.title + `" class="dropdown-item is-color-black">
                                            <i class="far fa-envelope"></i>&nbsp;` + window.vue.lang.share_email + `
                                        </a>
                                        <a onclick="window.vue.toggleActivityOptions(document.getElementById('activity-qo-` + elem.id + `'));" href="sms:000000000?body=` + window.location.origin + '/activity/' + elem.id + ` - ` + elem.title + `" class="dropdown-item is-color-black">
                                            <i class="fas fa-sms"></i>&nbsp;` + window.vue.lang.share_sms + `
                                        </a>
                                        <a href="javascript:void(0)" onclick="window.vue.copyToClipboard('` + window.location.origin + '/activity/' + elem.id + ` - ` + elem.title + `'); window.vue.toggleActivityOptions(document.getElementById('activity-qo-` + elem.id + `'));" class="dropdown-item is-color-black">
                                            <i class="far fa-copy"></i>&nbsp;` + window.vue.lang.share_clipboard + `
                                        </a>
                                        ` + userOptions + `
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="is-inline-block is-stretched">
                    <div class="activity-title is-wordbreak is-default-padding is-inline-block is-stretched">
                        <center><span><a class="is-def-color" href="` + window.location.origin + '/activity/' + elem.slug + `">` + ((elem.title.length > MAX_ACTIVITY_TITLE_LENGTH) ? elem.title.substr(0, MAX_ACTIVITY_TITLE_LENGTH) + '...': elem.title) + `</a></span> <span class="dropdown-trigger ` + ((tagcode.length > 0) ? '': 'is-hidden') + `" onclick="window.vue.toggleActivityTags(document.getElementById('activity-tags-` + elem.id + `'));"><i class="fas fa-hashtag is-pointer"></i></span></center>
                    </div>

                    ` + tagcode + `
                </div>

                <div class="activity-infos is-default-padding">
                    <center><div><i class="far fa-clock"></i>&nbsp;` + ((sameDate) ? elem.date_of_activity_from_display : elem.date_of_activity_from_display + ' - ' + elem.date_of_activity_till_display) + ' ' + elem.date_of_activity_time + `</div>
                        <div class="is-capitalized"><i class="fas fa-map-marker-alt"></i>&nbsp;` + elem.location + `</div></center>
                </div>

                <div class="activity-divider">
                    <hr/>
                </div>

                <div class="activity-information is-wordbreak is-default-side-padding">` + ((elem.description.length > MAX_ACTIVITY_DESCRIPTION_LENGTH) ? elem.description.substr(0, MAX_ACTIVITY_DESCRIPTION_LENGTH) + '...': elem.description) + `</div>

                <div class="activity-footer is-default-side-padding">
                    <div class="activity-footer-stats">
                        <div class="is-inline-block"><i class="fas fa-users"></i>&nbsp;` + elem.participants + `</div>
                        <div class="is-inline-block"><i class="far fa-comments"></i>&nbsp;` + elem.messages + `</div>
                        <div class="is-inline-block"><i class="far fa-eye"></i>&nbsp;` + elem.view_count + `</div>
                    </div>

                    <div class="activity-footer-view is-inline-block">
                        <a class="button is-transparent-green"  onclick="location.href = '` + window.location.origin + '/activity/' + elem.id + `';">` + this.lang.view + `</a>
                    </div>
                </div>
            </div>`;
            } else if (elem._type === 'ad') {
                html = '<div class="activity-ad">' + elem.code + '</div>';
            }

            return html;
        },

        renderActivitySmall: function(elem) {
            let dtFrom = new Date(elem.date_of_activity_from);
            let dtStrFrom = dtFrom.getFullYear() + "-" + dtFrom.getMonth() + "-" + dtFrom.getDay();
            let dtTill = new Date(elem.date_of_activity_till);
            let dtStrTill = dtTill.getFullYear() + "-" + dtTill.getMonth() + "-" + dtTill.getDay();
            
            let sameDate = dtStrFrom == dtStrTill;

            let html = `
                <div class="activity-small">
                    <div class="activity-small-title">
                        <a href="` + window.location.origin + '/activity/' + elem.slug + `">` + elem.title + `</a>
                    </div>

                    <div class="activity-small-infos">
                        <div class="activity-small-infos-left is-inline-block">
                            <div class="is-inline-block"><i class="fas fa-users is-color-dark-grey"></i>&nbsp;` + elem.participants + `</div>
                            <div class="is-inline-block"><i class="far fa-comments is-color-dark-grey"></i>&nbsp;` + elem.messages + `</div>
                            <div class="is-inline-block"><i class="far fa-eye is-color-dark-grey"></i>&nbsp;` + elem.view_count + `</div>
                        </div>

                        <div class="activity-small-infos-right is-inline-block is-color-dark-grey">
                            ` + ((sameDate) ? elem.date_of_activity_from_display : elem.date_of_activity_from_display + ' - ' + elem.date_of_activity_till_display) + ' ' + elem.date_of_activity_time + `
                        </div>
                    </div>
                </div>
            `;

            return html;
        },

        renderMessageListItem: function(item) {
            let message = item.lm.message;
            if (message.length > 20) {
                message = message.substr(0, 20) + '...';
            }

            let html = `
                <div class="messages-item ` + ((!item.lm.seen) ? 'is-new-message' : '') + `">
                    <div class="messages-item-avatar">
                        <img src="` + window.location.origin + `/gfx/avatars/` + item.lm.user.avatar + `">
                    </div>
        
                    <div class="messages-item-name">
                        <a href="` + window.location.origin + `/user/` + item.lm.user.name + `">` + item.lm.user.name + `</a>
                    </div>
        
                    <div class="messages-item-subject">
                        <a href="` + window.location.origin + `/messages/show/` + item.lm.id + `">` + item.lm.subject + `</a>
                    </div>

                    <div class="message-item-lastmsg">
                        <a href="` + window.location.origin + `/messages/show/` + item.lm.id + `">` + item.lm.sender.name + `: ` + message + `</a>
                    </div>
        
                    <div class="messages-item-date" title="` + item.lm.created_at + `">
                        ` + item.lm.diffForHumans + `
                    </div>
                </div>
            `;
        
            return html;
        },

        renderMessageItem: function(elem, self) {
            let align = '';
            if (elem.senderId === self) {
                align = 'message-align-right';
            } else {
                align = 'message-align-left';
            }

            let html = `
                <div class="message-thread ` + align + `">
                    <div class="message-thread-header">
                        <div class="message-thread-header-avatar">
                            <a href="` + window.location.origin + '/user/' + elem.sender.name + `"><img src="` + window.location.origin + '/gfx/avatars/' + elem.sender.avatar + `"></a>
                        </div>

                        <div class="message-thread-header-userinfo">
                            <div><a href="` + window.location.origin + '/user/' + elem.sender.name + `">` + elem.sender.name + `</a></div>
                            <div class="is-message-label-small" title="` + elem.created_at + `">` + elem.diffForHumans + `</div>
                        </div>

                        <div class="message-thread-header-subject">` + elem.subject + `</div>
                    </div>

                    <div class="message-thread-text">` + elem.message + `</div>
                </div>
            `;

            return html;
        },

        renderNotification: function(elem, newItem = false) {
            let icon = 'fas fa-info-circle';
            if (elem.type === 'PUSH_PARTICIPATED') {
                icon = 'fas fa-users';
            } else if (elem.type === 'PUSH_COMMENTED') {
                icon = 'far fa-comment';
            } else if (elem.type === 'PUSH_MENTIONED') {
                icon = 'fas fa-bolt';
            } else if (elem.type === 'PUSH_MESSAGED') {
                icon = 'far fa-comments';
            } else if (elem.type === 'PUSH_FAVORITED') {
                icon = 'far fa-star';
            } else if (elem.type === 'PUSH_CREATED') {
                icon = 'far fa-plus-square';
            } else if (elem.type === 'PUSH_CANCELED') {
                icon = 'fas fa-times-circle';
            } else if (elem.type === 'PUSH_FORUMREPLY') {
                icon = 'fas fa-landmark';
            }

            let html = `
                <div class="notification-item ` + ((newItem) ? 'is-new-notification' : '') + `" id="notification-item-` + elem.id + `">
                    <div class="notification-icon">
                        <div class="notification-item-icon"><i class="` + icon + ` fa-3x"></i></div>
                    </div>
                    <div class="notification-info">
                        <div class="notification-item-message is-color-grey-dark">` + elem.longMsg + `</div>
                        <div class="notification-item-message is-color-grey-light">` + elem.diffForHumans + `</div>
                    </div>
                </div>
            `;

            return html;
        },

        renderFavorite: function(elem) {
            let html = `
                <div class="favorites-item">
                    <div class="favorite-left">
                        <div class="favorite-item-avatar favorite-badge">
                            <img src="` + window.location.origin + '/gfx/avatars/' + elem.avatar + `" alt="avatar"/>
                            <span class="favnot-badge is-hidden" id="favorite-activity-count-` + elem.entityId + `"></span>
                        </div>

                        <div class="favorite-item-info">
                            <div class="is-color-grey-dark"><a href="` + window.location.origin + '/user/' + elem.entityId + `">` + elem.name + `</a>` + ((elem.verified) ? '&nbsp;<i class="far fa-check-circle" title="' + this.lang.verifiedUser +' "></i>' : '') + `</div>
                            <div title="` + elem.created_at + `" class="is-color-grey-light">Added: ` + elem.diffForHumans + `</div>
                        </div>
                    </div>

                    <div class="favorite-right">
                        <span title="Remove" class="is-pointer" onclick="location.href = '` + window.location.origin + '/user/' + elem.entityId + '/fav/remove' + `';"><i class="fas fa-times"></i></span>
                    </div>
                </div>
            `;

            return html;
        },

        renderForumItem: function(item) {
            let lastPoster = '';
            if (item.lastUser !== null) {
                lastPoster = `
                    <div class="last-poster is-pointer" onclick="location.href = '` + window.location.origin + '/forum/thread/' + item.lastUser.threadId + `/show';">
                        <div class="last-poster-avatar"><img src="` + window.location.origin + '/gfx/avatars/' + item.lastUser.avatar + `" alt="avatar"></div>
                        <div class="last-poster-userdata">
                            <div class="last-poster-name ">` + item.lastUser.name + `</div>
                            <div class="last-poster-date">` + item.lastUser.diffForHumans + `</div>
                        </div>
                    </div>
                `;
            }
            
            let html = `
                <div class="forum-item">
                    <div class="forum-title">
                        <div class="is-pointer is-breakall is-width-73-percent" onclick="location.href = '` + window.location.origin + '/forum/' + item.id + `/show';">` + item.name + `</div>
                        ` + lastPoster + `
                    </div>
                    <div class="forum-description is-pointer is-breakall" onclick="location.href = '` + window.location.origin + '/forum/' + item.id + `/show';">` + item.description + `</div>
                </div>
            `;
        
            return html;
        },
        
        renderForumThreadItem: function(item) {
            let flags = '';
            if (item.sticky) {
                flags += '<i class="fas fa-thumbtack"></i> ';
            }
            if (item.locked) {
                flags += '<i class="fas fa-lock"></i> ';
            }
        
            let html = `
                <div class="forum-thread">
                    <div class="forum-thread-infos">
                        <div class="forum-thread-info-id">#` + item.id + `</div>
                        <div class="forum-thread-info-title is-breakall is-pointer" onclick="location.href = '` + window.location.origin + '/forum/thread/' + item.id + `/show';">` + flags + ' ' + item.title + `</div>
                        <div class="forum-thread-info-lastposter">
                            <div class="forum-thread-info-lastposter-avatar"><a href="` + window.location.origin + '/user/' + item.user.id + `"><img src="` + window.location.origin + '/gfx/avatars/' + item.user.avatar + `" alt="avatar"/></a></div>
                            <div class="forum-thread-info-lastposter-userinfo">
                                <div class="forum-thread-info-lastposter-userinfo-name"><a href="` + window.location.origin + '/user/' + item.user.id + `">` + item.user.name + `</a></div>
                                <div class="forum-thread-info-lastposter-userinfo-date">` + item.user.diffForHumans + `</div>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        
            return html;
        },
        
        renderForumPostingItem: function(item, admin = false, owner = false) {
            let adminCode = '';
            if (admin) {
                adminCode = ` | <a href="javascript:void(0);" onclick="window.vue.lockForumPost(` + item.id + `);">` + window.vue.lang.lock + `</a>`;
            }
        
            if ((admin) && (!owner)) {
                owner = true;
            }
        
            let ownerCode = '';
            if (owner) {
                ownerCode = ` | <a href="javascript:void(0);" onclick="document.getElementById('forum-post-id').value = '` + item.id + `'; document.getElementById('forum-edit-thread-post-post').value = document.getElementById('forum-posting-message-` + item.id + `').innerText; window.vue.bShowEditForumPost = true;">` + window.vue.lang.edit + `</a>`;
            }
        
            if (item.locked) {
                item.message = '<i class="is-color-grey">' + item.message + '</i>';
            }
        
            let editedInfo = '';
            if ((item.created_at !== item.updated_at) && (!item.locked)) {
                editedInfo = '<br/><i class="is-color-grey is-font-small">' + window.vue.lang.forumPostEdited + ' ' + item.updatedAtDiff + '</i>';
            }
            
            let html = `
                <div class="forum-posting">
                    <div class="forum-posting-userinfo">
                        <div class="forum-posting-userinfo-avatar"><a href="` + window.location.origin + '/user/' + item.user.id + `"><img src="` + window.location.origin + '/gfx/avatars/' + item.user.avatar + `" alt="avatar"/></a></div>
                        <div class="forum-posting-userinfo-name"><a href="` + window.location.origin + '/user/' + item.user.id + `">` + item.user.name + `</a></div>
                    </div>
        
                    <div class="forum-posting-message">
                        <div class="forum-posting-message-content">
                            <div id="forum-posting-message-` + item.id + `" class="is-wordbreak">` + item.message + `</div> ` + editedInfo + `
                        </div>
        
                        <div class="forum-posting-message-footer">
                            <span class="is-color-grey" title="` + item.created_at + `">` + item.diffForHumans + `</span> | <a href="javascript:void(0);" onclick="window.vue.reportForumPost(` + item.id + `)">` + window.vue.lang.report + `</a>` + adminCode + ` ` + ownerCode + `
                        </div>
                    </div>
                </div>
            `;
        
            return html;
        },

        renderMarketItem: function(item) {
            let banner = window.location.origin + '/gfx/market/' + item.banner;

            let dropdownMenu = '';
            if ((window.user !== null) && (typeof window.user.id !== 'undefined') && (window.user.id !== item.user.id)) {
                dropdownMenu = `
                    <div class="mp-advert-dropdown">
                        <div class="dropdown is-right" id="mp-advert-dropdown-` + item.id + `">
                            <div class="dropdown-trigger">
                                <i class="fas fa-ellipsis-v is-pointer" onclick="window.vue.toggleContextMenu(document.getElementById('mp-advert-dropdown-` + item.id + `'));"></i>
                            </div>
                            <div class="dropdown-menu" role="menu">
                                <div class="dropdown-content">
                                    <a class="dropdown-item is-color-black" href="` + window.location.origin + '/marketplace/' + item.id + '/report' + `">
                                        ` + this.lang.report + `
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            }

            let userHint = window.vue.lang.marketplace_advert_by.replace(':name', item.user.name);

            let html = `
                <div class="mp-advert">
                    <div class="mp-advert-banner" style="background-image: url('` + banner + `');">
                        ` + dropdownMenu + `
                    </div>

                    <div class="mp-advert-info">
                        <div class="mp-advert-info-title">` + item.title + `</div>

                        <div class="mp-advert-info-description">` + item.description + `</div>
                    </div>

                    <div class="mp-advert-footer">
                        <div class="mp-advert-footer-inner">
                            <div class="mp-advert-footer-user"><a href="` + window.location.origin + '/user/' + item.user.slug + `">` + userHint + `</a></div>
                            <div class="mp-advert-footer-view"><a class="button is-transparent-green" href="` + item.link + `" onclick="window.vue.showLinkFilter('` + item.link + `'); return false;">Besuchen</a></div>
                        </div>
                    </div>
                </div>
            `;

            return html;
        },

        renderGalleryItem: function(item) {
            let image = window.location.origin + '/gfx/gallery/' + item.image_thumb;

            let dropdownMenu = '';
            if ((window.user !== null) && (typeof window.user.id !== 'undefined')) {
                let reportAction = '';
                if (window.user.id !== item.user.id) {
                    reportAction = `
                        <a class="dropdown-item is-color-black" href="` + window.location.origin + '/gallery/' + item.id + '/report' + `">
                            ` + this.lang.report + `
                        </a>
                    `;
                }

                let adminOrOwnerAction = '';
                if ((window.user.id == item.user.id) || ((window.user.data.admin) || (window.user.data.maintainer))) {
                    adminOrOwnerAction = `
                        <a class="dropdown-item is-color-black" href="` + window.location.origin + '/gallery/' + item.id + '/remove' + `">
                            ` + this.lang.remove + `
                        </a>
                    `;
                }

                dropdownMenu = `
                    <div class="gallery-item-dropdown">
                        <div class="dropdown is-right" id="gallery-item-dropdown-` + item.id + `">
                            <div class="dropdown-trigger">
                                <i class="fas fa-ellipsis-v is-pointer" onclick="window.vue.toggleContextMenu(document.getElementById('gallery-item-dropdown-` + item.id + `'));"></i>
                            </div>
                            <div class="dropdown-menu" role="menu">
                                <div class="dropdown-content">
                                    <a onclick="window.vue.toggleContextMenu(document.getElementById('gallery-item-dropdown-` + item.id + `'));" href="whatsapp://send?text=` + window.location.origin + '/gallery/item/' + item.slug + ` - ` + item.title + `" class="dropdown-item is-color-black">
                                        <i class="fab fa-whatsapp"></i>&nbsp;` + window.vue.lang.share_whatsapp + `
                                    </a>
                                    <a onclick="window.vue.toggleContextMenu(document.getElementById('gallery-item-dropdown-` + item.id + `'));" href="https://twitter.com/share?url=` + encodeURI(window.location.origin + '/gallery/item/' + item.slug) + `&text=` + item.title + `" class="dropdown-item is-color-black">
                                        <i class="fab fa-twitter"></i>&nbsp;` + window.vue.lang.share_twitter + `
                                    </a>
                                    <a onclick="window.vue.toggleContextMenu(document.getElementById('gallery-item-dropdown-` + item.id + `'));" href="https://www.facebook.com/sharer/sharer.php?u=` + window.location.origin + '/gallery/item/' + item.slug + `" class="dropdown-item is-color-black">
                                        <i class="fab fa-facebook"></i>&nbsp;` + window.vue.lang.share_facebook + `
                                    </a>
                                    <a onclick="window.vue.toggleContextMenu(document.getElementById('gallery-item-dropdown-` + item.id + `'));" href="mailto:name@domain.com?body=` + window.location.origin + '/gallery/item/' + item.slug + ` - ` + item.title + `" class="dropdown-item is-color-black">
                                        <i class="far fa-envelope"></i>&nbsp;` + window.vue.lang.share_email + `
                                    </a>
                                    <a onclick="window.vue.toggleContextMenu(document.getElementById('gallery-item-dropdown-` + item.id + `'));" href="sms:000000000?body=` + window.location.origin + '/gallery/item/' + item.slug + ` - ` + item.title + `" class="dropdown-item is-color-black">
                                        <i class="fas fa-sms"></i>&nbsp;` + window.vue.lang.share_sms + `
                                    </a>
                                    <a href="javascript:void(0)" onclick="window.vue.copyToClipboard('` + window.location.origin + '/gallery/item/' + item.slug + ` - ` + item.title + `'); window.vue.toggleContextMenu(document.getElementById('gallery-item-dropdown-` + item.id + `'));" class="dropdown-item is-color-black">
                                        <i class="far fa-copy"></i>&nbsp;` + window.vue.lang.share_clipboard + `
                                    </a>

                                    ` + ((reportAction.length > 0 || adminOrOwnerAction.length > 0) ? '<hr class="dropdown-divider">' : '') + `

                                    ` + reportAction + adminOrOwnerAction + `
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            }

            let userHint = window.vue.lang.gallery_item_by.replace(':name', item.user.name);

            let tags = '';
            if (item.tags.length > 0) {
                item.tags.forEach(function(tag, index) {
                    if (tag.length > 0) {
                        tags += `
                            <div class="gallery-item-info-tag">
                                <a href="` + window.location.origin + '/gallery?tag=' + tag + `">#` + tag + `</a>
                            </div>
                        `;
                    }
                });
            }

            if (tags.length <= 0) {
                tags = '<i>' + window.vue.lang.noTagsSpecified + '</i>';
            }

            let html = `
                <div class="gallery-item">
                    <div class="gallery-item-image is-pointer" style="background-image: url('` + image + `');" onclick="location.href = '` + window.location.origin + '/gallery/item/' + item.slug + `';"></div>

                    <div class="gallery-item-info">
                        <div class="gallery-item-info-title">
                            ` + item.title + `
                            ` + dropdownMenu + `
                        </div>

                        <div class="gallery-item-info-location"><i class="fas fa-map-marker-alt is-color-dark-grey"></i> ` + item.location + `</div>
                    
                        <div class="gallery-item-info-tags">
                            ` + tags + `
                        </div>
                    </div>

                    <div class="gallery-item-footer">
                        <div class="gallery-item-footer-inner">
                            <div class="gallery-item-footer-user"><a href="` + window.location.origin + '/user/' + item.user.slug + `">` + userHint + `</a></div>
                            <div class="gallery-item-footer-likes">
                                <span id="count-like-` + item.id + `">` + item.likes + `</span>&nbsp;
                                <span><a href="javascript:void(0);" onclick="window.vue.toggleLike(` + item.id + `, 'action-like-` + item.id + `', 'count-like-` + item.id + `');"><i class="` + ((item.hasLiked) ? 'fas' : 'far') + ` fa-heart" id="action-like-` + item.id + `"></i></a></span>
                            </div>
                        </div>
                    </div>
                </div>
            `;

            return html;
        },

        toggleLike: function(item, heart, count) {
            window.vue.ajaxRequest('get', window.location.origin + '/gallery/' + item + '/like', {}, function(response) {
                if (response.code == 200) {
                    if (response.action == 'liked') {
                        document.getElementById(heart).classList.add('fas');
                        document.getElementById(heart).classList.remove('far');

                        document.getElementById(count).innerHTML = parseInt(document.getElementById(count).innerHTML) + 1;
                    } else if (response.action == 'unliked') {
                        document.getElementById(heart).classList.remove('fas');
                        document.getElementById(heart).classList.add('far');

                        document.getElementById(count).innerHTML = parseInt(document.getElementById(count).innerHTML) - 1;
                    }
                }
            });
        },

        toggleNotifications: function(ident) {
            let obj = document.getElementById(ident);
            if (obj) {
                if (obj.style.display === 'block') {
                    obj.style.display = 'none';
                } else {
                    obj.style.display = 'block';
                }
            }
        },

        toggleFavorites: function(ident) {
            let obj = document.getElementById(ident);
            if (obj) {
                if (obj.style.display === 'block') {
                    obj.style.display = 'none';
                } else {
                    obj.style.display = 'block';
                }
            }
        },

        reportComment: function(id) {
            location.href = window.location.origin + '/comment/' + id + '/report';
        },

        lockComment: function(id) {
            if (confirm('Do you really want to lock the comment?')) {
                location.href = window.location.origin + '/comment/' + id + '/lock';
            }
        },

        reportForumPost: function(elemId) {
            window.vue.ajaxRequest('get', window.location.origin + '/forum/thread/post/' + elemId + '/report', {}, function(response) {
                alert(response.msg);
            });
        },

        lockUser: function(id) {
            if (confirm('Do you really want to lock this user?')) {
                location.href = window.location.origin + '/user/' + id + '/lock';
            }
        },

        lockForumPost: function(id) {
            if (confirm(window.vue.lang.confirmLockForumPost)) {
                window.vue.ajaxRequest('get', window.location.origin + '/forum/thread/post/' + id + '/lock', {}, function (response) {
                    alert(response.msg);
                });
            }
        },

        showEditComment: function(elemId) {
            document.getElementById('editCommentId').value = elemId;
            document.getElementById('editCommentText').value = document.getElementById('thread-text-' + elemId).innerHTML;
            window.vue.bShowEditComment = true;
        },

        fetchSubThreadPosts: function(parentId) {
            if (typeof window.subPosts === 'undefined') {
                window.subPosts = [];
            }

            if (typeof window.subPosts[parentId] === 'undefined') {
                window.subPosts[parentId] = null;
            }

            document.getElementById('sub-thread-' + parentId).innerHTML += '<center><i class="fas fa-spinner fa-spin" id="spinner-sub-thread-' + parentId + '"></i></center>';

            window.vue.ajaxRequest('get', window.location.origin + '/thread/' + parentId + '/sub' + ((window.subPosts[parentId] !== null) ? '?paginate=' + window.subPosts[parentId] : ''), {}, function(response){
                if (response.code == 200) {
                    document.getElementById('spinner-sub-thread-' + parentId).remove();

                    let html = '';
                    console.log(response.data);
                    response.data.forEach(function(elem, index) {
                        html += window.vue.renderThread(elem, elem.adminOrOwner, true, parentId)
                    });

                    document.getElementById('sub-thread-' + parentId).innerHTML += html;

                    if (response.last === false) {
                        if (document.getElementById('sub-comment-more-' + parentId) !== null) {
                            document.getElementById('sub-comment-more-' + parentId).remove();
                        }

                        document.getElementById('sub-thread-' + parentId).innerHTML += `<center><div id="sub-comment-more-` + parentId + `"><a class="is-color-grey" href="javascript:void(0)" onclick="window.vue.fetchSubThreadPosts(` + parentId + `)">View more</a></div></center>`;
                    }

                    if (response.data.length === 0) {
                        if (document.getElementById('sub-comment-more-' + parentId) !== null) {
                            document.getElementById('sub-comment-more-' + parentId).remove();
                        }
                    } else {
                        window.subPosts[parentId] = response.data[response.data.length - 1].id;
                    }
                }
            });
        },

        replyThread: function(parentId, text){
            this.ajaxRequest('post', window.location.origin + '/thread/' + parentId + '/reply', { text: text }, function(response){
                if (response.code === 200) {
                    location.href = window.location.origin + '/activity/' + response.comment.activityId + '#thread';
                }
            });
        },

        lockActivity: function(id) {
            if (confirm('Do you really want to lock the activity?')) {
                location.href = window.location.origin + '/activity/' + id + '/lock';
            }
        },

        showTabMenu: function(target) {
            let tabItems = ['tabProfile', 'tabSecurity', 'tabNotifications', 'tabGallery', 'tabMarketplace', 'tabMembership'];

            tabItems.forEach(function(elem, index) {
               if (elem !== target) {
                   document.getElementById(elem).classList.remove('is-active');
                   document.getElementById(elem + '-form').classList.add('is-hidden');
               }

               document.getElementById(target).classList.add('is-active');
               document.getElementById(target + '-form').classList.remove('is-hidden');
            });
        },

        markSeen: function() {
            this.ajaxRequest('get', window.location.origin + '/notifications/seen', {}, function(response) {
                if (response.code !== 200) {
                    console.log(response.msg);
                }
            });
        },

        togglePublicProfile: function(obj) {
            this.ajaxRequest('post', window.location.origin + '/settings/privacy/publicprofile', { value: obj.checked}, function(response) {
                if (response.code !== 200) {
                    obj.checked = !obj.checked;
                }
            });
        },

        queryLocation: function(src, dst, fill) {
            if (src.value.length >= 2) {
                this.ajaxRequest('get', window.location.origin + '/locations/query?term=' + src.value, {}, function(response) {
                    if (response.code == 200) {
                        let dest = document.getElementById('location-list-content-' + dst);
                        if (dest !== null) {
                            dest.innerHTML = '';

                            response.data.forEach(function(elem, index) {
                                dest.innerHTML += '<div class="dropdown-item is-pointer" onclick="document.getElementById(\'' + fill + '\').value = \'' + elem.name + '\'; document.getElementById(\'location-list-' + dst + '\').classList.remove(\'is-active\');">' + elem.name + '</div>';
                            });
                        }

                        let menu = document.getElementById('location-list-' + dst);
                        if (menu !== null) {
                            menu.classList.add('is-active');
                        }
                    }
                });
            } else {
                let menu = document.getElementById('location-list-' + dst);
                if (menu !== null) {
                    menu.classList.remove('is-active');
                }
            }
        },

        showLinkFilter: function(url) {
            if ((!url.startsWith('https://')) && (!url.startsWith('http://'))) {
                url = 'http://' + url;
            }

            let title = window.vue.lang.linkfilter_title.replace(':url', url);
            let hint = window.vue.lang.linkfilter_hint.replace(':url', url).replace(':project', window.vue.app_project);
            
            document.getElementById('linkfilter-url').value = url;
            document.getElementById('linkfilter-title').innerHTML = title;
            document.getElementById('linkfilter-hint').innerHTML = hint;

            window.vue.bShowLinkFilter = true;
        },
    }
});
