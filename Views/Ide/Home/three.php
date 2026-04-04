<link rel="stylesheet" href="/themes/assets/fonts/ide/ide.css">
<link rel="stylesheet" href="/themes/Higgs/css/three.css">
<div class="wrapper three">
    <div class="file-browser">
        <ul>
            <li class="folder folder-open">
                Dashboard
                <ul>
                    <li class="file">Accounts Module</li>
                    <li class="file">Help Module</li>
                </ul>
            </li>
            <li class="folder">
                Transfers
                <ul>
                    <li class="folder folder-open">
                        Quick
                        <ul>
                            <li class="file">Disclosures</li>
                            <li class="file">Modals</li>
                        </ul>
                    </li>
                    <li class="file">Classic</li>
                    <li class="folder">
                        Scheduled
                        <ul>
                            <li class="folder"> Calendar
                                <ul>
                                    <li class="file">Days</li>
                                    <li class="file">Months</li>
                                </ul>
                            </li>
                            <li class="file">Modals</li>
                        </ul>
                    </li>
                    <li class="file">History</li>
                </ul>
            </li>
        </ul>
    </div>
</div>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        var folders = document.querySelectorAll(".folder");
        var files = document.querySelectorAll(".file");

        folders.forEach(function (folder) {
            folder.addEventListener("click", function (e) {
                this.classList.toggle("folder-open");
                e.stopPropagation();
            });
        });

        files.forEach(function (file) {
            file.addEventListener("click", function (e) {
                e.stopPropagation();
            });
        });
    });

</script>