require 'rubygems'


desc "Default Task - Build Library"
task :default  => [:required] do
  Rake::Task['build'].execute
end


task :required do
    puts "Executind task: required"
end


desc "Copy Files to ProcessMaker"
task :build => [:required] do
    mode = "production"
    #argv1 = ARGV.last
    publicDir = Dir.pwd + "/workflow/public_html"

    # validate
    unless File.exists?(publicDir)
        puts "Seems it is not a ProcessMaker installation"
        exit(1)
    end

    ##
    # Build PMUI Library
    #
    if mode == "production"
        targetDir = publicDir + "/lib"
        pmUIFontsDir = targetDir + "/css/fonts"
    else
        targetDir = publicDir + "/lib-dev"
        pmUIFontsDir = pmUIDir + "/fonts"
    end

    jsTargetDir  = targetDir + "/js"
    cssTargetDir = targetDir + "/css"
    cssImagesTargetDir = cssTargetDir + "/images"
    imgTargetDir = targetDir + "/img"

    pmUIDir = targetDir + "/pmUI"

    prepareDirs([pmUIDir, jsTargetDir, cssTargetDir, cssImagesTargetDir, imgTargetDir, pmUIFontsDir])

    buildPmUi(targetDir, mode)

    #task argv1.to_sym do ; end
end

def buildPmUi(targetDir, mode)

    # Defining target directories
    homeDir = Dir.pwd + "/vendor/colosa/pmUI"
    pmUIDir = targetDir + "/pmUI"
    pmUIFontsDir = targetDir + "/fonts"
    jsTargetDir = targetDir + "/js"
    cssTargetDir = targetDir + "/css"
    imgTargetDir = targetDir + "/img"
    version = getVersion(homeDir)
    #

    puts "Generating Theme files"
    executeInto(homeDir, ["compileTheme[mafe]", "js"])

    puts "\nCopying lib files into: #{pmUIDir}"

    puts "*Copy build/js/min/pmui-#{version}.min.js -> pmui.min.js"
    system "cp #{homeDir}/build/js/pmui-#{version}.js #{pmUIDir}/pmui.min.js"

    puts "*Copy themes/mafe/build/pmui-mafe.css -> pmui.min.css"
    system "cp #{homeDir}/themes/mafe/build/pmui-mafe.css #{pmUIDir}/pmui.min.css"

    puts "*Copy themes/mafe/build/images/*.png -> css/images/"
    system "cp -Rf #{homeDir}/themes/mafe/build/images/*.png #{targetDir}/css/images/"

    puts "*Copy img/* to #{imgTargetDir}"
    system "cp -Rf #{homeDir}/img/* #{imgTargetDir}"


    jsLibFiles = {
        homeDir + "/libraries/restclient/restclient-min.js" => "restclient.min.js"
    }

    puts ""
    puts "Copying lib files into: #{jsTargetDir}"

    jsLibFiles.each do |src, target|
        puts "*Copy #{src} -> #{target}"
        system "cp #{src} #{jsTargetDir}/#{target}"
    end

    puts ""
    puts "Copying font files into: #{pmUIFontsDir}"
    #targetConfFiles = [
    #     homeDir + "/config/fonts.json"
    #]

    theme ="mafe"

    puts "Copying  fonts"
    puts "*Copy themes/#{theme}/fonts/* -> #{targetDir}"
    system "cp -R #{homeDir}/themes/#{theme}/fonts/* #{targetDir}"

end

def prepareDirs(dirs)
    homeDir = Dir.pwd

    puts "Preparing Directories..."

    dirs.each do |dir|
        if File.directory?(dir)
            puts "Removing #{dir}"
            system "rm -rf #{dir}"
        end
        Dir.mkdir(dir)
    end
end


def getVersion(path)
    if File.exists? path + '/VERSION.txt'
        version = File.read path + '/VERSION.txt'
    else
        version = "(unknown)"
    end

    return version.strip
end


def executeInto(path, tasks)
    Dir.chdir(path) do
	    tasks.each do |task|
            system "rake #{task}"
        end
	end
end
