(function() {
    'use strict';

    angular
        .module('app.main.mentorship')
        .controller('CalendarController', CalendarController);

    function CalendarController(MentorshipService, $rootScope, $localStorage, $mdDialog, $mdToast, $filter, $element, triLayout, triTheming, uiCalendarConfig, $scope) {
        var vm = this;
        vm.currentUser = $localStorage.currentUser;
        vm.accept = accept;
        vm.reject = reject;
        vm.changeMonth = changeMonth;
        vm.eventSources = [{
            events: []
        }];

        function changeMonth(direction) {
            uiCalendarConfig.calendars['triangular-calendar'].fullCalendar(direction);
            vm.eventSources[0].events.length = 0;
            getAllMeeting(vm.currentUser.accountID);
            console.log(vm.eventSources);
        }

        function getAppoint(mID) {
            MentorshipService.getAppoint(mID).then(function(response) {
                vm.appointList = response.data;
            });
        }

        function getAllMeeting(mID) {
            MentorshipService.getAllMeeting(mID).then(function(response) {
                vm.meetings = response.data;
                console.log(vm.meetings);
                for (var i = 0; i <= vm.meetings.length; i++) {
                    var dateobj = vm.meetings[i].start;
                    var dateobj2 = vm.meetings[i].end;

                    var randomPalette = pickRandomProperty(triTheming.palettes);
                    
                    vm.eventSources[0].events.push({
                        meetingID: vm.meetings[i].meetingID,
                        title: vm.meetings[i].topic,
                        start: moment(dateobj),
                        end: moment(dateobj2),
                        description: vm.meetings[i].reminder,
                        location: vm.meetings[i].location,
                        backgroundColor: triTheming.rgba(triTheming.palettes[randomPalette]['500'].value),
                        borderColor: triTheming.rgba(triTheming.palettes[randomPalette]['500'].value),
                        textColor: triTheming.rgba(triTheming.palettes[randomPalette]['500'].contrast),
                        palette: randomPalette

                    });
                }

            });
        }

        getAppoint(vm.currentUser.accountID);
        getAllMeeting(vm.currentUser.accountID);

        vm.calendarOptions = {
            contentHeight: 'auto',
            selectable: true,
            editable: true,
            header: false,
            viewRender: function(view) {
                // change day
                vm.currentDay = view.calendar.getDate();
                // update toolbar with new day for month name
                $rootScope.$broadcast('calendar-changeday', vm.currentDay);
                // update background image for month
                triLayout.layout.contentClass = 'calendar-background-image overlay-gradient-10 calendar-background-month-' + vm.currentDay.month();
            },

            dayClick: function(date, jsEvent, view) { //eslint-disable-line
                vm.currentDay = date;
            },

            eventClick: function(calEvent, jsEvent, view) { //eslint-disable-line
                $mdDialog.show({
                        controller: 'EventDialogController',
                        controllerAs: 'vm',
                        templateUrl: 'app/page/mentorship/calendar/event-dialog.html',
                        targetEvent: jsEvent,
                        focusOnOpen: false,
                        locals: {
                            dialogData: {
                                title: 'Edit Meeting',
                                confirmButtonText: 'Save'
                            },
                            event: calEvent,
                            edit: true
                        }
                    })
                    .then(function(event) {
                        var toastMessage = 'Event Updated';
                        if (angular.isDefined(event.deleteMe) && event.deleteMe === true) {
                            // remove the event from the calendar
                            uiCalendarConfig.calendars['triangular-calendar'].fullCalendar('removeEvents', event._id);
                            // change toast message
                            toastMessage = 'Event Deleted';
                        } else {
                            // update event
                            uiCalendarConfig.calendars['triangular-calendar'].fullCalendar('updateEvent', event);
                        }

                        // pop a toast
                        $mdToast.show(
                            $mdToast.simple()
                            .content($filter('triTranslate')(toastMessage))
                            .position('bottom right')
                            .hideDelay(2000)
                        );
                    });
            }
        };

        function pickRandomProperty(obj) {
            var result;
            var count = 0;
            for (var prop in obj) {
                if (Math.random() < 1 / ++count) {
                    result = prop;
                }
            }
            return result;
        }

        function accept(sID) {
            $mdDialog.show(
                $mdDialog.confirm()
                .title('Are You Sure?')
                .ok('Yes')
                .cancel('No')
            ).then(function(){
                acceptAppoint(sID);
            });
        }
        
        function acceptAppoint(sID){
            MentorshipService.acceptAppoint(vm.currentUser.accountID, sID);
            for (var i =0; i<= vm.appointList.length; i++){
                if (vm.appointList[i].id == sID){
                    vm.appointList.splice(i,1);
                }
            }
            vm.eventSources[0].events.length = 0;
            getAllMeeting(vm.currentUser.accountID);
            MentorshipService.sdMsg(vm.currentUser.accountID, sID, 3);
        }

        function rejectAppoint(sID){
            MentorshipService.rejectAppoint(vm.currentUser.accountID, sID);
            for (var i =0; i<= vm.appointList.length; i++){
                if (vm.appointList[i].id == sID){
                    vm.appointList.splice(i,1);
                }
            }
            MentorshipService.sdMsg(vm.currentUser.accountID, sID, 5);
        }

        function reject(sID) {
            $mdDialog.show(
                $mdDialog.confirm()
                .title('Are You Sure?')
                .textContent('Thank You')
                .ok('Yes')
                .cancel('No')
            ).then(function(){
                rejectAppoint(sID);
            });
        }
    }
})();
