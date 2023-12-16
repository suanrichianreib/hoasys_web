<style>
#loading-overlay {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(255, 255, 255, 0.8);
    z-index: 9999;
}

.spinner {
    border: 8px solid #f3f3f3;
    border-top: 8px solid #3498db;
    border-radius: 50%;
    width: 50px;
    height: 50px;
    margin: 20% auto;
    /* Adjust the margin to center the spinner */
    animation: spin 1s linear infinite;
}

@keyframes spin {
    0% {
        transform: rotate(0deg);
    }

    100% {
        transform: rotate(360deg);
    }
}

.whats-on-your-mind {
    background-color: #fff;
    padding: 15px;
    margin-bottom: 20px;
    border-radius: 12px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}

.forum-card {
    margin-bottom: 20px;
    border-radius: 15px;
    /* Rounded edges */
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    /* Shadow */
}

.forum-post {
    border: 1px solid #ddd;
    padding: 15px;
    background-color: #fff;
    border-radius: 12px !important;
}

.comments-section {
    margin-top: 20px;
}

.comment {
    border-top: 1px solid #ddd;
    padding: 10px 0;
}

.see-more-comments {
    cursor: pointer;
    color: #007bff;
}

.comment-icons {
    margin-top: 5px;
}

.load-more-btn {
    margin-top: 20px;
    text-align: center;
}
</style>

<div class="m-grid__item m-grid__item--fluid m-wrapper">
    <div class="m-subheader" style="padding-bottom: 10rem; background-color: #f0f1f7;">
        <div class="d-flex align-items-center">
            <div class="mr-auto">
                <h3 class="m-subheader__title" style="color: #073A4B;"> Forum </h3>
                <input type="hidden" id="refresh_data_Update">
            </div>
            <br>
        </div>
    </div>
    <div class="m-content" style="margin-top: -11rem !important;">

        <!-- What's on your mind? Section -->
        <div class="whats-on-your-mind">
            <h4>What's on your mind?</h4>
            <textarea class="form-control" rows="3" placeholder="Write your forum post..."></textarea>
            <div class="row mt-3">
                <div class="col-6">
                    <button class="btn btn-primary">Post Forum</button>
                </div>
                <div class="col-4 mt-3 text-right" style="font-size: smaller;"><i class="fa fa-eye"></i>&nbsp;&nbsp;<span> Viewer</span></div>
                <div class="col-2 text-left">
                    <div class="form-group">
                        <select class="form-control text-left" id="visibilitySelect">
                            <option value="admin">Admin</option>
                            <option value="officers">Officers</option>
                            <option value="homeowners">Homeowners</option>
                            <option value="admin_and_officers">Admin and Officers</option>
                            <option value="homeowners_only">Homeowners Only</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <!-- Forum 1 -->
        <div class="card forum-card">
            <div class="card-body forum-post">
                <h4>Forum Title 1</h4>
                <p>This is the content of the first forum post.</p>
                <div class="comments-section">
                    <!-- Comment 1 -->
                    <div class="comment">
                        <p>User 1: This is a comment on the first forum post. <span class="comment-icons"><i
                                    class="far fa-thumbs-up"></i> <i class="far fa-comment"></i></span></p>
                    </div>
                    <!-- Comment 2 -->
                    <div class="comment">
                        <p>User 2: Another comment on the first forum post. <span class="comment-icons"><i
                                    class="far fa-thumbs-up"></i> <i class="far fa-comment"></i></span></p>
                    </div>
                    <!-- Add more comments as needed -->
                    <!-- See More Comments Link -->
                    <div class="see-more-comments" onclick="showMoreComments()">See More Comments...</div>
                </div>
            </div>
        </div>

        <!-- Forum 2 -->
        <div class="card forum-card">
            <div class="card-body forum-post">
                <h4>Forum Title 2</h4>
                <p>This is the content of the second forum post.</p>
                <div class="comments-section">
                    <!-- Comment 1 -->
                    <div class="comment">
                        <p>User 3: This is a comment on the second forum post. <span class="comment-icons"><i
                                    class="far fa-thumbs-up"></i> <i class="far fa-comment"></i></span></p>
                    </div>
                    <!-- Comment 2 -->
                    <div class="comment">
                        <p>User 4: Another comment on the second forum post. <span class="comment-icons"><i
                                    class="far fa-thumbs-up"></i> <i class="far fa-comment"></i></span></p>
                    </div>
                    <!-- Add more comments as needed -->
                    <!-- See More Comments Link -->
                    <div class="see-more-comments" onclick="showMoreComments()">See More Comments...</div>
                </div>
            </div>
        </div>

        <!-- Load More Button -->
        <div class="load-more-btn">
            <button class="btn btn-primary" onclick="loadMoreContent()">Load More</button>
        </div>

    </div>
</div>

<!-- modal  -->
<form id="submit_concern_email" method="post">
    <div class="modal fade" id="concern_send_details" data-id="0" tabindex="-1" role="dialog"
        aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" style="max-width: 800px" role="document">
            <div class="modal-content">
                <div class="modal-body">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <div style="margin: 0 20px">
                        <div class="form-group m-form__group row">
                            <div style="margin: 20px auto;">
                                <h3 style="text-align: center!important;">Send Email</h3>
                                <h5 style="text-align: center!important;" class="text-muted" id="subheader"></h5>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xl-12">
                                <div class="form-group">
                                    <label for="recipient-name" class="m--font-bolder">Send To <span
                                            class="m--font-danger">*</span></label>
                                    <input type="hidden" id="concern_ID_Update">
                                    <input type="text" class="form-control m-input m-input--solid" id="send_to"
                                        autocomplete="off" required disabled>
                                </div>
                            </div>
                            <div class="col-xl-12">
                                <div class="form-group">
                                    <label for="recipient-name" class="m--font-bolder">Subject <span
                                            class="m--font-danger">*</span></label>
                                    <input type="text" class="form-control m-input m-input--solid" id="subject_concern"
                                        autocomplete="off">
                                </div>
                            </div>
                            <div class="col-xl-12">
                                <div class="form-group">
                                    <label class="m--font-bolder">Email Message to Sender</label>
                                    <textarea class="form-control m-input m-input--solid" style="height: 150px;"
                                        id="email_content" rows="3"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-metal" data-dismiss="modal">Close</button>&nbsp; <button
                        type="submit" class="btn btn-success">Send</button>
                </div>
            </div>
        </div>
    </div>
</form>
<form id="submit_concern_details" method="post">
    <div class="modal fade" id="concern_details" data-id="0" tabindex="-1" role="dialog"
        aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" style="max-width: 800px" role="document">
            <div class="modal-content">
                <div class="modal-body">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <div style="margin: 0 20px">
                        <div class="form-group m-form__group row">
                            <div style="margin: 20px auto;">
                                <h3 style="text-align: center!important;">Concern #<span
                                        id="concern_id_display">#00001</span></h3>
                                <h5 style="text-align: center!important;" class="text-muted" id="subheader"></h5>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xl-6">
                                <label class="m--font-bolder">Concern Title: </label>
                                <span id="concern_title_display">This is a sample Title</span>
                            </div>
                            <div class="col-xl-6">
                                <label class="m--font-bolder">Date Sent: </label>
                                <span id="concern_date_display">2023-05-7 7:00pm</span>
                            </div>
                            <div class="col-xl-6">
                                <label class="m--font-bolder">Sender: </label>
                                <span id="concern_sender_display">Joshua Quijada</span>
                            </div>
                            <div class="col-xl-6">
                                <label class="m--font-bolder">Status: </label>
                                <span id="concern_status_display">Unpublished</span>
                            </div>
                            <div class="col-12">
                                <label class="m--font-bolder">Concern Description: </label>
                                <p id="concern_desc_display">Lorem ipsum dolor sit amet, consectetur adipiscing elit,
                                    sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim
                                    veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo
                                    consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum
                                    dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident,
                                    sunt in culpa qui officia deserunt mollit anim id est laborum.</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-metal" data-dismiss="modal">Close</button>&nbsp;
                    <button type="button" id="solve_btn" class="btn btn-success btn-icon" style="display:none;"><i
                            class="fa fa-check"></i> <span>Mark as solved </span></button>&nbsp;
                    <button type="button" id="unsolve_btn" class="btn btn-danger btn-icon" style="display:none;"><i
                            class="fa fa-remove"></i> <span>Mark as Unsolved </span></button>&nbsp;
                </div>
            </div>
        </div>
    </div>
</form>
<!-- <script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script> -->
<script src="<?php echo base_url() ?>assets/src/custom/js/forum/forum.js?<?php echo $date_time; ?>">
</script>
<script>
// Add your custom scripts here
function showMoreComments() {
    // Implement logic to load more comments or navigate to a new page with more comments
    console.log('Show more comments clicked');
}
</script>
