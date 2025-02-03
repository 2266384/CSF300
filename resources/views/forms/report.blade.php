<x-layout Title="Report">
    <div class="col content">


        <form id="report-issue" action="{{ route('send.email') }}" method="POST" action="{{ route('send.email') }}" enctype="multipart/form-data">
            @csrf
            <div class="row">
                <div class="form-group">
                    <label for="subject_input">Subject</label>
                    <input type="text" class="form-control" id="subject_input" name="subject" placeholder="Subject">
                </div>
                <div class="form-group">
                    <label for="message_input">Message</label>
                    <textarea class="form-control" id="message_input" name="message" rows="10"></textarea>
                </div>
            </div>
                <div class="form-group">
                    <label for="file_upload">Attach File</label>
                    <input type="file" class="form-control-file" id="file_upload" name="file_upload">
                </div>
                <button type="submit" class="btn btn-primary col-md-1" name="reportButton">Submit</button>
        </form>

    </div>
</x-layout>
