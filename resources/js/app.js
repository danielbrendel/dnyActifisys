/*
    ComAct (dnyComAct) developed by Daniel Brendel

    (C) 2019 - 2020 by Daniel Brendel

    Version: 1.0
    Contact: dbrendel1988<at>gmail<dot>com
    GitHub: https://github.com/danielbrendel/

    Released under the MIT license
*/

require('./bootstrap');

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
            this.invalidDate(document.getElementById('caDate'), document.getElementById('activity-date-hint'), btn);
            this.invalidRequiredInput(document.getElementById('caLocation'), btn);
        },

        toggleActivityOptions: function(elem) {
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

        copyToClipboard: function(text) {
            const el = document.createElement('textarea');
            el.value = text;
            document.body.appendChild(el);
            el.select();
            document.execCommand('copy');
            document.body.removeChild(el);
            alert('Text has been copyied to clipboard!');
        },

        renderThread: function(elem, adminOrOwner = false, isSubComment = false, parentId = 0) {
            let options = '';

            if (adminOrOwner) {
                options = `
            <a onclick="window.vue.showEditComment(` + elem.id + `); window.vue.toggleCommentOptions(document.getElementById('thread-options-` + elem.id + `'));" href="javascript:void(0)" class="dropdown-item">
                <i class="far fa-edit"></i>&nbsp;Edit
            </a>
            <a onclick="window.vue.lockComment(` + elem.id + `); window.vue.toggleCommentOptions(document.getElementById('thread-options-` + elem.id + `'));" href="javascript:void(0)" class="dropdown-item">
                <i class="fas fa-times"></i>&nbsp;Lock
            </a>
            <hr class="dropdown-divider">
        `;
            }

            let expandThread = '';
            if (elem.subCount > 0) {
                expandThread = `<div class="thread-footer-subthread is-inline-block is-centered"><a class="is-color-grey" href="javascript:void(0)" onclick="window.vue.fetchSubThreadPosts(` + elem.id + `)">Expand thread</a></div>`;
            }

            let replyThread = `<div class="is-inline-block float-right"><a class="is-color-grey" href="javascript:void(0)" onclick="document.getElementById('thread-reply-parent').value = '` + ((isSubComment) ? parentId : elem.id) + `'; document.getElementById('thread-reply-textarea').value = '` + elem.user.name + `: '; window.vue.bShowReplyThread = true;">Reply</a></div>`;

            let html = `
        <div id="thread-` + elem.id + `" class="thread-elem ` + ((isSubComment) ? 'is-sub-comment': '') + `">
            <a name="` + elem.id + `"></a>

            <div class="thread-header">
                <div class="thread-header-avatar is-inline-block">
                    <img width="24" height="24" src="` + window.location.origin + `/gfx/avatars/` + elem.user.avatar + `" class="is-pointer" onclick="location.href = '` + window.location.origin + `/u/` + elem.user.id + `';" title="">
                </div>

                <div class="thread-header-info is-inline-block">
                    <div><a href="` + window.location.origin + `/u/` + elem.user.id + `" class="is-color-grey">` + elem.user.name + `</a></div>
                    <div title="` + elem.created_at + `">` + elem.diffForHumans + `</div>
                </div>

                <div class="thread-header-options is-inline-block">
                    <div class="dropdown is-right" id="thread-options-` + elem.id + `">
                        <div class="dropdown-trigger" onclick="window.vue.toggleCommentOptions(document.getElementById('thread-options-` + elem.id + `'));">
                            <i class="fas fa-ellipsis-v is-pointer"></i>
                        </div>
                        <div class="dropdown-menu" role="menu">
                            <div class="dropdown-content">
                                ` + options + `

                                <a href="javascript:void(0)" onclick="window.vue.reportComment(` + elem.id + `); window.vue.toggleCommentOptions(document.getElementById('thread-options-` + elem.id + `'));" class="dropdown-item">
                                    Report
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="thread-text is-color-grey" id="thread-text-` + elem.id + `">
                ` + elem.text + `
            </div>

            <div class="thread-footer">
                ` + expandThread + `
                ` + replyThread + `
            </div>

            <div id="sub-thread-` + elem.id + `"></div>
        </div>
    `;

            return html;
        },

        reportComment: function(id) {
            location.href = window.location.origin + '/comment/' + id + '/report'
        },

        lockComment: function(id) {
            if (confirm('Do you really want to lock the comment?')) {
                location.href = window.location.origin + '/comment/' + id + '/lock'
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

                        document.getElementById('sub-thread-' + parentId).innerHTML += `<center><div id="sub-comment-more-` + parentId + `"><a href="javascript:void(0)" onclick="window.vue.fetchSubThreadPosts(` + parentId + `)">View more</a></div></center>`;
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

        cancelActivity: function(id) {
            if (confirm('Do you really want to cancel the activity?')) {
                location.href = window.location.origin + '/activity/' + id + '/cancel';
            }
        },

        lockActivity: function(id) {
            if (confirm('Do you really want to lock the activity?')) {
                location.href = window.location.origin + '/activity/' + id + '/lock';
            }
        },
    }
});
