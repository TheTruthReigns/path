angular
    .module('app.main.mentorship', [])
    .factory('MentorshipService', function($http, $filter) {

        return {
            getYourMentor : function(schoolID, programID) {
            	var query = $http.get('http://unicomhk.net/path2/php/study.php?action=getYourMentor&schoolID=' + schoolID +'&programID=' + programID);               
                return query;
            },

            chooseMentor : function(mID, sID){
            	$http.get('http://unicomhk.net/path2/php/mentorship.php?action=chooseMentor&mID=' + mID + '&sID=' + sID);
            },

            getOthersAccount : function(accountID){
                var query = $http.get('http://unicomhk.net/path2/php/mentorship.php?action=getOthersAccount&accountID=' + accountID);               
                return query;
            },

            mkAppointment: function(appoint) {
                var query = $http.get('http://unicomhk.net/path2/php/mentorship.php?action=mkAppointment&mID=' + appoint.mID + '&sID=' + appoint.sID + '&date=' + appoint.date + '&time=' + appoint.time + '&msg=' + appoint.msg);
                return query;
            },

            joinMeeting: function(appoint) {
                var query = $http.get('http://unicomhk.net/path2/php/mentorship.php?action=joinMeeting&mID=' + appoint.mID + '&sID=' + appoint.sID + '&date=' + appoint.date + '&time=' + appoint.time);
                return query;
            },

            getMeetingInfo: function(sID) {
                var query = $http.get('http://unicomhk.net/path2/php/mentorship.php?action=getMeetingInfo&sID=' + sID);
                return query;
            },

            sdMsg: function(sender, receiver,type){
                $http.get('http://unicomhk.net/path2/php/mentorship.php?action=sdMsg&sender=' + sender + '&receiver=' + receiver + '&type=' + type);
            },

            getAppoint: function(mID){
                var query = $http.get('http://unicomhk.net/path2/php/mentorship.php?action=getAppoint&mID=' + mID);
                return query;
            },

            getAllMeeting: function(mID){
                var query = $http.get('http://unicomhk.net/path2/php/mentorship.php?action=getAllMeeting&mID=' + mID);
                return query;
            },

            acceptAppoint: function(mID,sID){
                $http.get('http://unicomhk.net/path2/php/mentorship.php?action=acceptAppoint&mID=' + mID + '&sID=' + sID );
            },

            rejectAppoint: function(mID,sID){
                $http.get('http://unicomhk.net/path2/php/mentorship.php?action=rejectAppoint&mID=' + mID + '&sID=' + sID );
            },

            updateMeeting: function(event){
                $http.get('http://unicomhk.net/path2/php/mentorship.php?action=updateMeeting&meetingID=' + event.meetingID 
                    + '&location=' + event.location
                    + '&topic=' + event.title
                    + '&description=' + event.description
                    + '&date=' + event.start.format("YYYY-MM-DD")
                    + '&start=' + event.start.format("HH:mm")
                    + '&end=' + event.end.format("HH:mm")
                );
            },

            deleteMeeting: function(event){
                $http.get('http://unicomhk.net/path2/php/mentorship.php?action=deleteMeeting&meetingID=' + event.meetingID);
            },

            getNote: function(mentor){
                var query = $http.get('http://unicomhk.net/path2/php/mentorship.php?action=getNote&mentorID=' + mentor.accountID);
                return query;
            },
        };
    });