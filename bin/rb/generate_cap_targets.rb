#!/usr/bin/env ruby

require 'erb'

template_file = File.read("#{__dir__}/cap_file_template.rb.erb")
template = ERB.new(template_file)

sites = `knife data bag show sites`
sites.split("\n").each do |site_id|
  site_info = `knife data bag show sites #{site_id}`

  site = {
    url: site_info.match(/url:\s+(.+)$/)[1],
    id: site_id
  }

  File.write("#{__dir__}/../../config/deploy/#{site_id}.rb", template.result(binding))
end

