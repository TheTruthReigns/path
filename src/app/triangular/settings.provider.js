(function() {
    'use strict';

    angular
        .module('triangular')
        .provider('triSettings', settingsProvider);

    /* @ngInject */
    function settingsProvider() {
        // Provider
        var settings = {
            languages: [],
            name: '',
            logo: '',
            copyright: '',
            version: '',
            skin :''
        };

        this.addLanguage = addLanguage;
        this.setLogo = setLogo;
        this.setName = setName;
        this.setCopyright = setCopyright;
        this.setVersion = setVersion;
        this.setSkin = setSkin;
        function addLanguage(newLanguage) {
            settings.languages.push(newLanguage);
        }

        function setLogo(logo) {
            settings.logo = logo;
        }

        function setName(name) {
            settings.name = name;
        }

        function setCopyright(copy) {
            settings.copyright = copy;
        }

        function setVersion(version) {
            settings.version = version;
        }

        function setSkin(skin){
            settings.skin = skin;
        }


        // Service
        this.$get = function() {
            return {
                languages: settings.languages,
                name: settings.name,
                copyright: settings.copyright,
                logo: settings.logo,
                version: settings.version,
                skin: settings.skin
            };
        };
    }
})();

