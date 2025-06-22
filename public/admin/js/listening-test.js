$(document).ready(() => {
  // File input label update with preview
  $(".custom-file-input").on("change", function () {
    const fileName = $(this).val().split("\\").pop()
    $(this).next(".custom-file-label").addClass("selected").html(fileName)

    // Audio preview
    if (this.files && this.files[0]) {
      const file = this.files[0]
      if (file.type.startsWith("audio/")) {
        let audioPreview = $(this).closest(".form-group").find(".audio-preview")
        if (audioPreview.length === 0) {
          audioPreview = $('<div class="audio-preview mt-2"></div>')
          $(this).closest(".form-group").append(audioPreview)
        }

        const audioUrl = URL.createObjectURL(file)
        audioPreview.html(`
                    <label><strong>Preview:</strong></label>
                    <audio controls class="d-block w-100">
                        <source src="${audioUrl}" type="${file.type}">
                        Your browser does not support the audio element.
                    </audio>
                    <small class="text-muted">File: ${fileName} (${(file.size / 1024 / 1024).toFixed(2)} MB)</small>
                `)
      }
    }
  })

  // Delete confirmation
  $(".delete-section-form, .delete-question-form").on("submit", function (e) {
    e.preventDefault()
    const type = $(this).hasClass("delete-section-form") ? "section" : "question"
    const message =
      type === "section"
        ? "Are you sure you want to delete this section? This will also delete all questions and audio files."
        : "Are you sure you want to delete this question?"

    if (confirm(message)) {
      this.submit()
    }
  })

  // Add Question Modal
  $("#addQuestionModal").on("show.bs.modal", function (event) {
    const button = $(event.relatedTarget)
    const sectionId = button.data("section-id")
    const modal = $(this)
    console.log("Section ID:", sectionId);
    // Reset form
    modal.find("form")[0].reset()
    modal.find(".audio-preview").remove()
    modal.find(".custom-file-label").removeClass("selected").html("Choose file")

    // Thiết lập action cho form - QUAN TRỌNG
    modal.find("form").attr("action", `/admin/listening-sections/${sectionId}/questions`)

    // Get section info from data attributes or find in DOM
    const sectionCard = button.closest(".section-card")
    const questionType = sectionCard.find(".badge-info").text().toLowerCase()
    const questionCount = sectionCard.find(".question-card").length

    modal.find("#question_section_id").val(sectionId)
    modal.find("#question_order").val(questionCount + 1)

    // Show/hide appropriate fields based on question type
    if (questionType === "single") {
        $("#singleQuestionAudio").show()
        $("#multipleQuestionTiming").hide()
        $("#question_audio_file").prop("required", true)
    } else {
        $("#singleQuestionAudio").hide()
        $("#multipleQuestionTiming").show()
        $("#question_audio_file").prop("required", false)
    }

    updateCorrectAnswerOptions()
})

  // Edit Section Modal
  $(".edit-section-btn").on("click", function () {
    const id = $(this).data("id")
    const title = $(this).data("title")
    const instructions = $(this).data("instructions")
    const questionType = $(this).data("question-type")
    const order = $(this).data("order")

    const form = $("#editSectionForm")
    form.attr("action", "/admin/listening-sections/" + id)
    form.find("#edit_title").val(title)
    form.find("#edit_instructions").val(instructions)
    form.find("#edit_question_type").val(questionType)
    form.find("#edit_order").val(order)

    // Reset file input
    form.find(".custom-file-label").removeClass("selected").html("Choose file")
    form.find(".audio-preview").remove()
  })

  // Edit Question Modal
  $(".edit-question-btn").on("click", function () {
    const id = $(this).data("id")
    const question = $(this).data("question")
    const options = $(this).data("options")
    const correctAnswer = $(this).data("correct-answer")
    const audioStartTime = $(this).data("audio-start-time")
    const audioEndTime = $(this).data("audio-end-time")
    const order = $(this).data("order")

    const form = $("#editQuestionForm")
    form.attr("action", "/admin/listening-questions/" + id)
    form.find("#edit_question_text").val(question)
    form.find("#edit_audio_start_time").val(audioStartTime || "")
    form.find("#edit_audio_end_time").val(audioEndTime || "")
    form.find("#edit_question_order").val(order)

    // Reset file input
    form.find(".custom-file-label").removeClass("selected").html("Choose file")
    form.find(".audio-preview").remove()

    // Populate options
    const optionsContainer = $("#edit-options-container")
    optionsContainer.empty()

    $.each(options, (index, option) => {
      const letter = String.fromCharCode(65 + index) // A, B, C, ...
      const removeBtn =
        index > 1
          ? `
                <div class="input-group-append">
                    <button type="button" class="btn btn-danger remove-option-btn">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            `
          : ""

      const html = `
                <div class="input-group mb-2">
                    <div class="input-group-prepend">
                        <div class="input-group-text">${letter}</div>
                    </div>
                    <input type="text" class="form-control edit-option" name="options[]" value="${option}" required>
                    ${removeBtn}
                </div>
            `
      optionsContainer.append(html)
    })

    updateEditCorrectAnswerOptions(correctAnswer)

    // Determine if we need to show audio upload or timing fields
    const sectionType = $(this).closest(".section-card").find(".badge-info").text().toLowerCase()
    if (sectionType === "single") {
      $("#edit_singleQuestionAudio").show()
      $("#edit_multipleQuestionTiming").hide()
    } else {
      $("#edit_singleQuestionAudio").hide()
      $("#edit_multipleQuestionTiming").show()
    }
  })

  // Add option button
  $("#add-option-btn").on("click", () => {
    const optionsCount = $("#options-container .input-group").length
    const letter = String.fromCharCode(65 + optionsCount) // A, B, C, ...

    const html = `
            <div class="input-group mb-2">
                <div class="input-group-prepend">
                    <div class="input-group-text">${letter}</div>
                </div>
                <input type="text" class="form-control option" name="options[]" required>
                <div class="input-group-append">
                    <button type="button" class="btn btn-danger remove-option-btn">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
        `

    $("#options-container").append(html)
    updateCorrectAnswerOptions()
  })

  // Edit add option button
  $("#edit-add-option-btn").on("click", () => {
    const optionsCount = $("#edit-options-container .input-group").length
    const letter = String.fromCharCode(65 + optionsCount) // A, B, C, ...

    const html = `
            <div class="input-group mb-2">
                <div class="input-group-prepend">
                    <div class="input-group-text">${letter}</div>
                </div>
                <input type="text" class="form-control edit-option" name="options[]" required>
                <div class="input-group-append">
                    <button type="button" class="btn btn-danger remove-option-btn">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
        `

    $("#edit-options-container").append(html)
    updateEditCorrectAnswerOptions()
  })

  // Remove option button (delegated event)
  $(document).on("click", ".remove-option-btn", function () {
    if ($("#options-container .input-group").length > 2 || $("#edit-options-container .input-group").length > 2) {
      $(this).closest(".input-group").remove()

      // Renumber the options
      $("#options-container .input-group-text").each(function (index) {
        $(this).text(String.fromCharCode(65 + index))
      })

      $("#edit-options-container .input-group-text").each(function (index) {
        $(this).text(String.fromCharCode(65 + index))
      })

      updateCorrectAnswerOptions()
      updateEditCorrectAnswerOptions()
    } else {
      alert("You must have at least 2 options.")
    }
  })

  // Update options when they change
  $(document).on("input", ".option", () => {
    updateCorrectAnswerOptions()
  })

  $(document).on("input", ".edit-option", () => {
    updateEditCorrectAnswerOptions()
  })

  function updateCorrectAnswerOptions() {
    const correctAnswerSelect = $("#correct_answer")
    const currentValue = correctAnswerSelect.val()
    correctAnswerSelect.empty()

    $('#options-container input[name="options[]"]').each(function (index) {
      const optionText = $(this).val()
      if (optionText.trim()) {
        const selected = optionText === currentValue ? "selected" : ""
        correctAnswerSelect.append(`<option value="${optionText}" ${selected}>${optionText}</option>`)
      }
    })
  }

  function updateEditCorrectAnswerOptions(selectedValue = null) {
    const correctAnswerSelect = $("#edit_correct_answer")
    correctAnswerSelect.empty()

    $('#edit-options-container input[name="options[]"]').each(function (index) {
      const optionText = $(this).val()
      if (optionText.trim()) {
        const selected = optionText === selectedValue ? "selected" : ""
        correctAnswerSelect.append(`<option value="${optionText}" ${selected}>${optionText}</option>`)
      }
    })
  }

  // Form validation before submit
  $("form").on("submit", function (e) {
    const form = $(this)

    // Check if audio file is required but not provided
    const requiredAudioInput = form.find('input[type="file"][required]')
    if (requiredAudioInput.length > 0 && !requiredAudioInput[0].files.length) {
      e.preventDefault()
      alert("Please select an audio file.")
      return false
    }

    // Check file size
    form.find('input[type="file"]').each(function () {
      if (this.files.length > 0) {
        const file = this.files[0]
        const maxSize = $(this).data("max-size") || 20 * 1024 * 1024 // 20MB default

        if (file.size > maxSize) {
          e.preventDefault()
          alert(`File size too large. Maximum allowed size is ${maxSize / 1024 / 1024}MB.`)
          return false
        }

        // Check file type
        if (!file.type.startsWith("audio/")) {
          e.preventDefault()
          alert("Please select a valid audio file.")
          return false
        }
      }
    })
  })

  // Auto-save draft functionality (optional)
  let autoSaveTimer
  $("input, textarea, select").on("input change", () => {
    clearTimeout(autoSaveTimer)
    autoSaveTimer = setTimeout(() => {
      // You can implement auto-save functionality here
      console.log("Auto-save triggered")
    }, 5000) // Save after 5 seconds of inactivity
  })
})

// Utility functions
function formatFileSize(bytes) {
  if (bytes === 0) return "0 Bytes"
  const k = 1024
  const sizes = ["Bytes", "KB", "MB", "GB"]
  const i = Math.floor(Math.log(bytes) / Math.log(k))
  return Number.parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + " " + sizes[i]
}

function formatDuration(seconds) {
  const minutes = Math.floor(seconds / 60)
  const remainingSeconds = seconds % 60
  return `${minutes}:${remainingSeconds.toString().padStart(2, "0")}`
}
