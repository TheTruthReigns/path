(function() {
    'use strict';

    angular.module('app.main.study')
        .controller('MessagingController', MessagingController);

    /* @ngInject */
    function MessagingController (StudyService, AccountService, $localStorage, $state) {
     	var vm = this;
     	vm.currentUser = $localStorage.currentUser;
        vm.baseState = $state.current;
        vm.openMail = openMail;

        StudyService.getTest($localStorage.currentUser.accountID).then(function(response) {
            vm.test = response.data;
        });

        StudyService.contactList($localStorage.currentUser.accountID).then(function(response) {
            vm.list = response.data;
        });


        // opens an email
        function openMail(contact) {
            $state.go('triangular.messaging.chat' , {
                chatID: contact.chatId,
                icon: contact.chatAccountIcon
            });
            contact.unread = false;
            vm.selectedMail = contact.chatId;
        }

        // returns back to email list
        function openlist() {
            $state.go(vm.baseState.name);
        }
     }
    
})();