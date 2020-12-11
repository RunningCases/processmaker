require 'rubygems'
require 'json'

desc "Default Task - Build Library"
task :default  => [:required] do
  Rake::Task['build'].execute
end


task :required do
    begin
        require 'json'
    rescue LoadError
        puts "JSON gem not found.\nInstall it by running 'gem install json'"
        exit(1)
    end
    begin
        require 'ftools'
    rescue LoadError
        puts "JSON gem not found.\nInstall it by running 'gem install ftools'"
        exit(1)
    end
end

task :log do
    puts getLog
end

desc "Build Front-End for ProcessMaker"
task :build => [:required] do
    mode = "production"
    #argv1 = ARGV.last
    publicDir = Dir.pwd + "/workflow/public_html"

    # validate
    unless File.exists?(publicDir)
        puts "Seems it is not a ProcessMaker installation"
        exit(1)
    end
    if mode == "production"
        targetDir = publicDir + "/lib"
        pmUIFontsDir = targetDir + "/fonts"
    else
        targetDir = publicDir + "/lib-dev"
        pmUIFontsDir = pmUIDir + "/fonts"
    end

    jsTargetDir  = targetDir + "/js"
    cssTargetDir = targetDir + "/css"
    cssImagesTargetDir = cssTargetDir + "/images"
    imgTargetDir = targetDir + "/img"

    pmUIDir = targetDir + "/pmUI"
    mafeDir = targetDir + "/mafe"
    pmdynaformDir = targetDir + "/pmdynaform"

    generateEnviromentVariables
    prepareDirs([targetDir, pmUIDir, mafeDir, pmdynaformDir, jsTargetDir, cssTargetDir, cssImagesTargetDir, imgTargetDir, pmUIFontsDir])

    buildPmUi(Dir.pwd + "/vendor/colosa/pmUI", targetDir, mode)
    buildPmdynaform(Dir.pwd + "/vendor/colosa/pmDynaform", targetDir, mode)
    buildPmdynaformZip(Dir.pwd + "/vendor/colosa/pmDynaform", publicDir)
    buildMafe(Dir.pwd + "/vendor/colosa/MichelangeloFE", targetDir, mode)



    pmuiHash = getHash(Dir.pwd + "/vendor/colosa/pmUI")
    mafeHash = getHash(Dir.pwd + "/vendor/colosa/MichelangeloFE")
    pmdynaformHash = getHash(Dir.pwd + "/vendor/colosa/pmDynaform")

    puts "Building file: Task Scheduler".cyan
    system "npm run build --prefix #{Dir.pwd}/vendor/colosa/taskscheduler"
    system "cp -Rf #{Dir.pwd}/vendor/colosa/taskscheduler/taskscheduler #{targetDir}/taskscheduler"
    system "cp  #{Dir.pwd}/vendor/colosa/taskscheduler/public/index.html #{targetDir}/taskscheduler"

    puts "\n\n"
    puts "Building: Authentication Sources".cyan
    system "npm install --loglevel=error --prefix #{Dir.pwd}/workflow/engine/methods/authenticationSources"
    system "npm run build --prefix #{Dir.pwd}/workflow/engine/methods/authenticationSources"

    hashVendors = pmuiHash+"-"+mafeHash
    ## Building minified JS Files
    puts "Building file: " + "/js/mafe-#{hashVendors}.js".cyan
    mafeCompresedFile = targetDir + "/js/mafe-#{hashVendors}.js"
    mafeCompresedContent = ""

    getJsIncludeFiles().each do |filename|
        mafeCompresedContent += File.read filename
        mafeCompresedContent += "\n"
    end

    File.open(mafeCompresedFile, 'w+') do |writeFile|
        writeFile.write mafeCompresedContent
    end

    #Building minified CSS Files
    puts "Building file: " + "/css/mafe-#{hashVendors}.css".cyan
    mafeCompresedFile = targetDir + "/css/mafe-#{hashVendors}.css"
    mafeCompresedContent = ""

    getCssIncludeFiles().each do |filename|
        mafeCompresedContent += File.read filename
        mafeCompresedContent += "\n"
    end

    File.open(mafeCompresedFile, 'w+') do |writeFile|
        writeFile.write mafeCompresedContent
    end

    # Create buildhash file
    puts "create file: " + "/buildhash".cyan
    File.open(targetDir+"/buildhash", 'w+') do |writeFile|
        writeFile.write hashVendors
    end

    puts "create file: " + "/versions".cyan
    versions = {
        :pmui_ver => getVersion(Dir.pwd + "/vendor/colosa/pmUI"),
        :pmui_hash => pmuiHash,
        :mafe_ver => getVersion(Dir.pwd + "/vendor/colosa/MichelangeloFE"),
        :mafe_hash => mafeHash,
        :pmdynaform_ver => getVersion(Dir.pwd + "/vendor/colosa/pmDynaform"),
        :pmdynaform_hash => pmdynaformHash
    }
    File.open(targetDir+"/versions", 'w+') do |writeFile|
        writeFile.write versions.to_json
    end

    File.open(targetDir+"/lib-pmui.log", 'w+') do |writeFile|
        writeFile.write getLogFrom(Dir.pwd + "/vendor/colosa/pmUI")
    end

    File.open(targetDir+"/lib-mafe.log", 'w+') do |writeFile|
        writeFile.write getLogFrom(Dir.pwd + "/vendor/colosa/MichelangeloFE")
    end

    File.open(targetDir+"/lib-pmdynaform.log", 'w+') do |writeFile|
        writeFile.write getLogFrom(Dir.pwd + "/vendor/colosa/pmDynaform")
    end

    File.open(targetDir+"/processmaker.log", 'w+') do |writeFile|
        writeFile.write getLog()
    end

    puts "-- DONE --\n".bold
    #task argv1.to_sym do ; end
end

def generateEnviromentVariables()
    puts "Creating System Constants..."
    content = "var __env = __env || {};"
    file = File.read('./config/enviromentvariables.json')
    dataUser = JSON.parse(file)
    content = content + "__env.USER_GUEST = " + JSON.generate(dataUser['constants']['userguest'])
    content = content + "; __env.pmVariable = " + JSON.generate(dataUser['validation']['pmVariable'])
    dir = "vendor/colosa/MichelangeloFE/src/enviroment/"
    # create a directory enviroment
    FileUtils.mkdir_p(dir)
    File.open(dir +'constants.js', 'w') { |fileWrite|
        fileWrite.write content + ';'
    }
end

def buildPmUi(homeDir, targetDir, mode)
    puts "\nBuilding PMUI library".green.bold

    # Defining target directories
    pmUIDir = targetDir + "/pmUI"
    pmUIFontsDir = targetDir + "/fonts"
    jsTargetDir = targetDir + "/js"
    cssTargetDir = targetDir + "/css"
    imgTargetDir = targetDir + "/img"
    version = getVersion(homeDir)

    puts "Generating Theme files"
    themeDir = Dir.pwd + "/vendor/colosa/MichelangeloFE/themes/mafe"
    executeInto(homeDir, ["compileTheme[#{themeDir}]", "js"])
    puts "\nCopying lib files into: #{pmUIDir}".bold
    copyFiles({
        "#{homeDir}/build/js/pmui-#{version}.js" => "#{pmUIDir}/pmui.min.js",
        "#{themeDir}/build/pmui-mafe.css" => "#{pmUIDir}/pmui.min.css",
        "#{themeDir}/build/images/*" => "#{targetDir}/css/images/",
        "#{homeDir}/img/*" => "#{imgTargetDir}"
    })

    puts "\nCopying lib files into: #{jsTargetDir}".bold
    copyFiles({homeDir + "/libraries/restclient/restclient-min.js" => "#{jsTargetDir}/restclient.min.js"})

    puts "\nCopying font files into: #{pmUIFontsDir}".bold
    theme = "mafe"
    copyFiles({"#{homeDir}/themes/#{theme}/fonts/*" => "#{pmUIFontsDir}"})

    puts "\nPMUI Build Finished".magenta
end

def buildPmdynaform(homeDir, targetDir, mode)
  puts "\nBuilding PmDynaform library".green.bold

  # Defining target directories
  pmdynaformDir = targetDir + "/pmdynaform"

  executeInto(homeDir, [ "default"])

  require 'fileutils'
  Dir.mkdir("#{pmdynaformDir}/build")
  FileUtils.cp_r(Dir["#{homeDir}/build/*"],"#{pmdynaformDir}/build")
  Dir.mkdir("#{pmdynaformDir}/libs")
  FileUtils.cp_r(Dir["#{homeDir}/libs/*"],"#{pmdynaformDir}/libs")

  template = ""
  config = File.read "#{homeDir}/config/templates.json"
  json = JSON.parse config
  json.each do |key|
    s = ""
    key["files"].each do |source|
      s += File.read "#{homeDir}/#{source}"
      s += "\n"
    end
    template += s
  end

  htmlTemplates=["pmdynaform.html"]
  htmlTemplates.each do |htmlTemplate|

    FileUtils.cp("#{Dir.pwd}/workflow/engine/templates/cases/#{htmlTemplate}", "#{pmdynaformDir}/build/#{htmlTemplate}")

    target = "#{pmdynaformDir}/build/#{htmlTemplate}"
    html = File.read target
    while html['###TEMPLATES##'] do
      html['###TEMPLATES###'] = template
    end
    File.open(target, 'w+') do |file|
      file.write html
    end
  end

  puts "\nPmDynaform Build Finished!".magenta
end

def buildPmdynaformZip(homeDir, targetDir)
  puts "\nBuilding Compress Zip library".green.bold
  executeInto(homeDir, [ "mobile"])
  copyFiles({homeDir + "/build-prod-zip/build-prod.zip" => targetDir + "/build-prod.zip"})
  File.chmod(0777, targetDir + "/build-prod.zip")
  puts "\nPmDynaform Zip Build Finished!".magenta
end

def buildMafe(homeDir, targetDir, mode)
    puts "\nBuilding PM Michelangelo FE".green.bold

    ###
    # Defining target directories
    mafeDir      = targetDir + "/mafe"
    jsTargetDir  = targetDir + "/js"
    cssTargetDir = targetDir + "/css"
    imgTargetDir = targetDir + "/img"
    ##

    executeInto(homeDir, ["rmdir", "dir", "compass", "compress_js_files", "compress_app_files"])
    #executeInto(homeDir, ["dir", "mafe"])

    puts "\nCopying files into: #{mafeDir}".bold
    copyFiles({
        "#{homeDir}/lib/jQueryUI/images/*.png" => "#{cssTargetDir}/images/",
        "#{homeDir}/build/js/designer.js" => "#{mafeDir}/designer.min.js",
        "#{homeDir}/build/js/mafe.js" => "#{mafeDir}/mafe.min.js",
        "#{homeDir}/build/css/mafe.css" => "#{mafeDir}/mafe.min.css",
        "#{homeDir}/img/*.*" => "#{imgTargetDir}"
    })

    puts "\nCopying lib files into: #{jsTargetDir}".bold
    copyFiles({
        "#{homeDir}/lib/wz_jsgraphics/wz_jsgraphics.js" => "#{jsTargetDir}/wz_jsgraphics.js",
        "#{homeDir}/lib/jQuery/jquery-1.10.2.min.js" => "#{jsTargetDir}/jquery-1.10.2.min.js",
        "#{homeDir}/lib/underscore/underscore-min.js" => "#{jsTargetDir}/underscore-min.js",
        "#{homeDir}/lib/jQueryUI/jquery-ui-1.10.3.custom.min.js" => "#{jsTargetDir}/jquery-ui-1.10.3.custom.min.js",
        "#{homeDir}/lib/jQueryLayout/jquery.layout.min.js" => "#{jsTargetDir}/jquery.layout.min.js",
        "#{homeDir}/lib/modernizr/modernizr.js" => "#{jsTargetDir}/modernizr.js"
    })
    system "cp -rf #{homeDir}/src/formDesigner/img/* #{mafeDir}/../img"

    puts "\nMichelangelo FE Build Finished\n".magenta
end

def prepareDirs(dirs)    
    puts "Preparing Directories..."

    dirs.each do |dir|
        if File.directory?(dir)
            if !File.writable?(dir)
                raise "Error, directory " + dir + " is not writable."
            end

            FileUtils.rm_rf(dir)
        end
        
        begin
          puts 'create '.green + dir
          FileUtils.mkdir_p(dir)
        rescue Exception => e
          puts ' (failed)'.red
          raise RuntimeError, e.message
        end
    end
end


def getVersion(path)
    version = ""
    Dir.chdir(path) do
        version = `rake version`
        version = version.strip
    end

    if version.lines.count > 1
        version = version.split("\n").last
    end

    return version
end


def getHash(path)
    hash = ""
    Dir.chdir(path) do
        hash = `git rev-parse --short HEAD`
    end

    return hash.strip
end


def getLogFrom(path)
    log = ""

    Dir.chdir(path) do
        log = `git log -30 --pretty='[%cr] %h %d %s <%an>' --no-merges`
    end

    return log.strip
end

def executeInto(path, tasks, ret = nil)
    output = ''
    
    Dir.chdir(path) do
	    tasks.each do |task|
            system "rake #{task}" or raise "An error was raised executing task '#{task}' into #{path}".red
        end
	end

    return output
end

def copyFiles(files)
    files.each do |from, to|
        puts "   copy ".green + from.gsub(Dir.pwd+'/vendor/colosa/', '')
        puts '   into '.green + to.gsub(Dir.pwd, '')
        
        system('cp -Rf '+from+' '+to+' 2>&1') or raise "Can't copy into directory #{to}".red
    end
end

def getJsIncludeFiles
    includeFiles = [
        "workflow/public_html/lib/js/wz_jsgraphics.js",
        "workflow/public_html/lib/js/jquery-1.10.2.min.js",
        "workflow/public_html/lib/js/underscore-min.js",
        "workflow/public_html/lib/js/jquery-ui-1.10.3.custom.min.js",
        "workflow/public_html/lib/js/jquery.layout.min.js",
        "workflow/public_html/lib/js/modernizr.js",
        "workflow/public_html/lib/js/restclient.min.js",
        "workflow/public_html/lib/pmUI/pmui.min.js",
        "workflow/public_html/lib/mafe/mafe.min.js",
        "workflow/public_html/lib/mafe/designer.min.js",
        "gulliver/js/tinymce/jscripts/tiny_mce/tiny_mce.js",

        "gulliver/js/tinymce/jscripts/tiny_mce/plugins/pmGrids/editor_plugin.js",
        "gulliver/js/tinymce/jscripts/tiny_mce/plugins/pmSimpleUploader/editor_plugin.js",
        "gulliver/js/tinymce/jscripts/tiny_mce/plugins/pmVariablePicker/editor_plugin.js",
        "gulliver/js/tinymce/jscripts/tiny_mce/plugins/visualchars/editor_plugin.js",
        "gulliver/js/tinymce/jscripts/tiny_mce/plugins/xhtmlxtras/editor_plugin.js",
        "gulliver/js/tinymce/jscripts/tiny_mce/plugins/wordcount/editor_plugin.js",
        "gulliver/js/tinymce/jscripts/tiny_mce/plugins/table/editor_plugin.js",
        "gulliver/js/tinymce/jscripts/tiny_mce/plugins/template/editor_plugin.js",
        "gulliver/js/tinymce/jscripts/tiny_mce/plugins/visualblocks/editor_plugin.js",
        "gulliver/js/tinymce/jscripts/tiny_mce/plugins/preview/editor_plugin.js",
        "gulliver/js/tinymce/jscripts/tiny_mce/plugins/print/editor_plugin.js",
        "gulliver/js/tinymce/jscripts/tiny_mce/plugins/style/editor_plugin.js",
        "gulliver/js/tinymce/jscripts/tiny_mce/plugins/save/editor_plugin.js",
        "gulliver/js/tinymce/jscripts/tiny_mce/plugins/tabfocus/editor_plugin.js",
        "gulliver/js/tinymce/jscripts/tiny_mce/plugins/searchreplace/editor_plugin.js",
        "gulliver/js/tinymce/jscripts/tiny_mce/plugins/paste/editor_plugin.js",
        "gulliver/js/tinymce/jscripts/tiny_mce/plugins/media/editor_plugin.js",
        "gulliver/js/tinymce/jscripts/tiny_mce/plugins/lists/editor_plugin.js",
        "gulliver/js/tinymce/jscripts/tiny_mce/plugins/insertdatetime/editor_plugin.js",
        "gulliver/js/tinymce/jscripts/tiny_mce/plugins/example/editor_plugin.js",
        "gulliver/js/tinymce/jscripts/tiny_mce/plugins/pagebreak/editor_plugin.js",
        "gulliver/js/tinymce/jscripts/tiny_mce/plugins/example_dependency/editor_plugin.js",
        "gulliver/js/tinymce/jscripts/tiny_mce/plugins/noneditable/editor_plugin.js",
        "gulliver/js/tinymce/jscripts/tiny_mce/plugins/fullpage/editor_plugin.js",
        "gulliver/js/tinymce/jscripts/tiny_mce/plugins/layer/editor_plugin.js",
        "gulliver/js/tinymce/jscripts/tiny_mce/plugins/legacyoutput/editor_plugin.js",
        "gulliver/js/tinymce/jscripts/tiny_mce/plugins/fullscreen/editor_plugin.js",
        "gulliver/js/tinymce/jscripts/tiny_mce/plugins/iespell/editor_plugin.js",
        "gulliver/js/tinymce/jscripts/tiny_mce/plugins/inlinepopups/editor_plugin.js",
        "gulliver/js/tinymce/jscripts/tiny_mce/plugins/autoresize/editor_plugin.js",
        "gulliver/js/tinymce/jscripts/tiny_mce/plugins/contextmenu/editor_plugin.js",
        "gulliver/js/tinymce/jscripts/tiny_mce/plugins/advlist/editor_plugin.js",
        "gulliver/js/tinymce/jscripts/tiny_mce/plugins/autolink/editor_plugin.js",
        "gulliver/js/tinymce/jscripts/tiny_mce/plugins/directionality/editor_plugin.js",
        "gulliver/js/tinymce/jscripts/tiny_mce/plugins/emotions/editor_plugin.js",
        "gulliver/js/tinymce/jscripts/tiny_mce/themes/advanced/editor_template.js",
        "gulliver/js/tinymce/jscripts/tiny_mce/plugins/advhr/editor_plugin.js",
        "gulliver/js/tinymce/jscripts/tiny_mce/plugins/advlink/editor_plugin.js",
        "gulliver/js/tinymce/jscripts/tiny_mce/plugins/advimage/editor_plugin.js",
        "gulliver/js/tinymce/jscripts/tiny_mce/plugins/nonbreaking/editor_plugin.js",

        "gulliver/js/codemirror/lib/codemirror.js",
        "gulliver/js/codemirror/addon/hint/show-hint.js",
        "gulliver/js/codemirror/addon/hint/javascript-hint.js",
        "gulliver/js/codemirror/addon/hint/sql-hint.js",
        "gulliver/js/codemirror/addon/hint/php-hint.js",
        "gulliver/js/codemirror/addon/hint/html-hint.js",
        "gulliver/js/codemirror/mode/javascript/javascript.js",
        "gulliver/js/codemirror/addon/edit/matchbrackets.js",
        "gulliver/js/codemirror/mode/htmlmixed/htmlmixed.js",
        "gulliver/js/codemirror/mode/xml/xml.js",
        "gulliver/js/codemirror/mode/css/css.js",
        "gulliver/js/codemirror/mode/clike/clike.js",
        "gulliver/js/codemirror/mode/php/php.js",
        "gulliver/js/codemirror/mode/sql/sql.js"
    ]

    return includeFiles
end

def getCssIncludeFiles
    return [
        "gulliver/js/codemirror/lib/codemirror.css",
        "gulliver/js/codemirror/addon/hint/show-hint.css",
#        DEPRECATED
#        "gulliver/js/tinymce/jscripts/tiny_mce/themes/advanced/skins/o2k7/ui.css",
#        "gulliver/js/tinymce/jscripts/tiny_mce/themes/advanced/skins/o2k7/ui_silver.css",
#        "gulliver/js/tinymce/jscripts/tiny_mce/plugins/inlinepopups/skins/clearlooks2/window.css",
#        "gulliver/js/tinymce/jscripts/tiny_mce/themes/advanced/skins/o2k7/content.css",
#
        "workflow/public_html/lib/pmUI/pmui.min.css",
        "workflow/public_html/lib/mafe/mafe.min.css"
    ]
end

class String
    def black;          "\033[30m#{self}\033[0m" end
    def red;            "\033[31m#{self}\033[0m" end
    def green;          "\033[32m#{self}\033[0m" end
    def brown;          "\033[33m#{self}\033[0m" end
    def blue;           "\033[34m#{self}\033[0m" end
    def magenta;        "\033[35m#{self}\033[0m" end
    def cyan;           "\033[36m#{self}\033[0m" end
    def gray;           "\033[37m#{self}\033[0m" end
    def bg_black;       "\033[40m#{self}\0330m"  end
    def bg_red;         "\033[41m#{self}\033[0m" end
    def bg_green;       "\033[42m#{self}\033[0m" end
    def bg_brown;       "\033[43m#{self}\033[0m" end
    def bg_blue;        "\033[44m#{self}\033[0m" end
    def bg_magenta;     "\033[45m#{self}\033[0m" end
    def bg_cyan;        "\033[46m#{self}\033[0m" end
    def bg_gray;        "\033[47m#{self}\033[0m" end
    def bold;           "\033[1m#{self}\033[22m" end
    def reverse_color;  "\033[7m#{self}\033[27m" end
end

def getLog
    output = `git log -30 --pretty='[%cr] %h %d %s <%an>' --no-merges`
    return output
end